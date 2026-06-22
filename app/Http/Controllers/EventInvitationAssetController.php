<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\DefaultInvitationBackgroundRenderer;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventInvitationAssetController extends Controller
{
    public function showBackground(Event $event): StreamedResponse
    {
        abort_unless($event->invitation_background_path !== null, 404);
        abort_unless(Storage::disk('public')->exists($event->invitation_background_path), 404);

        return Storage::disk('public')->response($event->invitation_background_path);
    }

    public function showDefaultBackground(DefaultInvitationBackgroundRenderer $renderer): Response
    {
        return response($renderer->render(), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
