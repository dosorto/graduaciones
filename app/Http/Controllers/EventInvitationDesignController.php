<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Support\QrCodeSvg;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventInvitationDesignController extends Controller
{
    public function edit(Request $request, Event $event): View
    {
        $this->authorizeOwnership($request, $event);

        $previewCode = $event->invitations()->first()?->code ?? 'SAMPLECODE01';

        return view('events.invitation-design', [
            'event' => $event,
            'previewCode' => $previewCode,
            'previewQrSvg' => QrCodeSvg::generate($previewCode, $event->invitationQrSize()),
            'qrPositions' => Event::invitationQrPositions(),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);

        $validated = $request->validate([
            'invitation_background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_invitation_background' => ['nullable', 'boolean'],
            'invitation_qr_position' => ['required', 'string', 'in:'.implode(',', Event::invitationQrPositions())],
            'invitation_qr_size' => ['required', 'integer', 'min:80', 'max:320'],
        ]);

        $updates = [
            'invitation_qr_position' => $validated['invitation_qr_position'],
            'invitation_qr_size' => $validated['invitation_qr_size'],
        ];

        if (($validated['remove_invitation_background'] ?? false) && $event->invitation_background_path) {
            Storage::disk('public')->delete($event->invitation_background_path);
            $updates['invitation_background_path'] = null;
        }

        if ($request->hasFile('invitation_background')) {
            if ($event->invitation_background_path) {
                Storage::disk('public')->delete($event->invitation_background_path);
            }

            $path = $request->file('invitation_background')->store('event-invitation-backgrounds', 'public');
            $updates['invitation_background_path'] = $path;
        }

        $event->update($updates);

        return redirect()
            ->route('events.invitation-design.edit', $event)
            ->with('status', 'Diseno de invitacion actualizado correctamente.');
    }

    private function authorizeOwnership(Request $request, Event $event): void
    {
        abort_unless($request->user()?->canAccessEvent($event), 403);
    }
}
