<?php

namespace App\Services;

use App\Models\EventGuest;
use App\Models\EventInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvitationSyncService
{
    public function syncForGuest(EventGuest $guest): void
    {
        DB::transaction(function () use ($guest): void {
            $guest->loadMissing('event', 'invitations');

            $existing = $guest->invitations()->orderBy('sequence_number')->get();
            $targetCount = $guest->invitation_count;
            $currentCount = $existing->count();

            if ($currentCount < $targetCount) {
                for ($sequence = $currentCount + 1; $sequence <= $targetCount; $sequence++) {
                    EventInvitation::create([
                        'event_id' => $guest->event_id,
                        'event_guest_id' => $guest->id,
                        'sequence_number' => $sequence,
                        'code' => $this->uniqueCode(),
                        'public_token' => Str::uuid()->toString(),
                    ]);
                }

                return;
            }

            if ($currentCount <= $targetCount) {
                return;
            }

            $extraCount = $currentCount - $targetCount;

            $guest->invitations()
                ->whereNull('used_at')
                ->orderByDesc('sequence_number')
                ->limit($extraCount)
                ->delete();
        });
    }

    public function addOne(EventGuest $guest): EventInvitation
    {
        return DB::transaction(function () use ($guest): EventInvitation {
            $guest->refresh();

            $invitation = EventInvitation::create([
                'event_id' => $guest->event_id,
                'event_guest_id' => $guest->id,
                'sequence_number' => ((int) $guest->invitations()->max('sequence_number')) + 1,
                'code' => $this->uniqueCode(),
                'public_token' => Str::uuid()->toString(),
            ]);

            $guest->forceFill([
                'invitation_count' => $guest->invitation_count + 1,
            ])->saveQuietly();

            return $invitation;
        });
    }

    public function removeOne(EventInvitation $invitation): void
    {
        DB::transaction(function () use ($invitation): void {
            $invitation->refresh();

            if ($invitation->used_at !== null) {
                abort(422, 'No se puede eliminar una invitacion que ya fue utilizada.');
            }

            $guest = $invitation->guest()->firstOrFail();
            $removedSequence = $invitation->sequence_number;

            $invitation->delete();

            EventInvitation::query()
                ->where('event_guest_id', $guest->id)
                ->where('sequence_number', '>', $removedSequence)
                ->orderBy('sequence_number')
                ->get()
                ->each(function (EventInvitation $pending): void {
                    $pending->update([
                        'sequence_number' => $pending->sequence_number - 1,
                    ]);
                });

            $guest->forceFill([
                'invitation_count' => $guest->invitations()->count(),
            ])->saveQuietly();
        });
    }

    private function uniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(12));
        } while (EventInvitation::where('code', $code)->exists());

        return $code;
    }
}
