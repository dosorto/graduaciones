<?php

namespace App\Http\Controllers;

use App\Models\EventInvitation;
use App\Services\InvitationImageRenderer;
use App\Support\QrCodeSvg;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;

class PublicInvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = EventInvitation::query()
            ->with(['event', 'guest'])
            ->where('public_token', $token)
            ->firstOrFail();

        return view('invitations.public', [
            'invitation' => $invitation,
            'event' => $invitation->event,
            'guest' => $invitation->guest,
            'qrSvg' => QrCodeSvg::generate($invitation->code, 210),
        ]);
    }

    public function image(string $token, InvitationImageRenderer $renderer): Response
    {
        $invitation = EventInvitation::query()
            ->with(['event', 'guest'])
            ->where('public_token', $token)
            ->firstOrFail();

        return response($renderer->render($invitation), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="invitacion-'.$invitation->code.'.png"',
            'Cache-Control' => 'no-store, private',
        ]);
    }
}
