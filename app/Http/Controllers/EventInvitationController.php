<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\EventInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventInvitationController extends Controller
{
    public function showGuest(Request $request, Event $event, EventGuest $guest): View
    {
        $this->authorizeOwnership($request, $event);
        abort_unless($guest->event_id === $event->id, 404);

        $guest->load(['event', 'invitations.validator']);

        return view('events.guest-invitations', [
            'event' => $event,
            'guest' => $guest,
            'normalizedPhone' => $this->normalizedPhone($guest->phone),
        ]);
    }

    public function share(Request $request, Event $event, EventGuest $guest, EventInvitation $invitation): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);
        abort_unless($guest->event_id === $event->id, 404);
        abort_unless($invitation->event_guest_id === $guest->id && $invitation->event_id === $event->id, 404);

        $whatsAppUrl = $this->whatsAppUrl($guest, $invitation);

        abort_unless($whatsAppUrl !== null, 422, 'Telefono no valido para WhatsApp.');

        if ($invitation->sent_at === null) {
            $invitation->update(['sent_at' => Carbon::now()]);
        }

        return redirect()->away($whatsAppUrl);
    }

    public function add(Request $request, Event $event, EventGuest $guest): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);
        abort_unless($guest->event_id === $event->id, 404);

        app(\App\Services\InvitationSyncService::class)->addOne($guest);

        return redirect()
            ->route('events.guests.invitations.show', [$event, $guest])
            ->with('status', 'Se agrego una nueva invitacion automaticamente.');
    }

    public function destroy(Request $request, Event $event, EventGuest $guest, EventInvitation $invitation): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);
        abort_unless($guest->event_id === $event->id, 404);
        abort_unless($invitation->event_guest_id === $guest->id && $invitation->event_id === $event->id, 404);

        app(\App\Services\InvitationSyncService::class)->removeOne($invitation);

        return redirect()
            ->route('events.guests.invitations.show', [$event, $guest])
            ->with('status', 'La invitacion fue eliminada y el total de cupos se actualizo.');
    }

    private function authorizeOwnership(Request $request, Event $event): void
    {
        abort_unless($request->user()?->canAccessEvent($event), 403);
    }

    private function whatsAppUrl(EventGuest $guest, EventInvitation $invitation): ?string
    {
        $phone = $this->normalizedPhone($guest->phone);

        if ($phone === null) {
            return null;
        }

        $event = $guest->event;
        $message = collect([
            'INVITACION',
            "Fecha: {$event->event_date->format('d/m/Y')}",
            'Hora: '.optional($event->event_time)->format('H:i'),
            "Lugar: {$event->venue}",
            '',
            'DESCARGA TU INVITACION:',
            $invitation->publicUrl(),
            '',
            "Codigo unico: {$invitation->code}",
            'Esta Invitacion es valida para un Invitado, Presentar esta Invitacion para su acceso el dia del Evento',
        ])->implode("\n");

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    private function normalizedPhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === '') {
            return null;
        }

        if (Str::startsWith($digits, '504')) {
            return $digits;
        }

        if (strlen($digits) === 8) {
            return '504'.$digits;
        }

        return $digits;
    }
}
