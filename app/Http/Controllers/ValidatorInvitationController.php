<?php

namespace App\Http\Controllers;

use App\Models\EventInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ValidatorInvitationController extends Controller
{
    public function index(Request $request): View
    {
        $code = trim((string) $request->query('code', ''));
        $invitation = null;

        if ($code !== '') {
            $invitation = EventInvitation::query()
                ->with(['event', 'guest', 'validator'])
                ->where('code', strtoupper($code))
                ->first();
        }

        return view('validator.dashboard', [
            'lookupCode' => $code,
            'invitation' => $invitation,
            'recentValidations' => EventInvitation::query()
                ->with(['event', 'guest', 'validator'])
                ->whereNotNull('used_at')
                ->latest('used_at')
                ->limit(10)
                ->get(),
        ]);
    }

    public function consume(Request $request, EventInvitation $invitation): RedirectResponse
    {
        $updated = EventInvitation::query()
            ->whereKey($invitation->id)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
                'validated_by_user_id' => $request->user()->id,
            ]);

        if ($updated === 0) {
            return redirect()
                ->route('validator.dashboard', ['code' => $invitation->code])
                ->with('status', 'La invitacion ya habia sido utilizada.');
        }

        return redirect()
            ->route('validator.dashboard', ['code' => $invitation->code])
            ->with('status', 'Invitacion validada correctamente.');
    }
}
