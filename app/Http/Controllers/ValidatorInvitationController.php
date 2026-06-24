<?php

namespace App\Http\Controllers;

use App\Models\EventInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidatorInvitationController extends Controller
{
    public function index(Request $request): View
    {
        $lookupCode = trim((string) $request->query('code', ''));
        $normalizedLookup = $this->normalizeLookupValue($lookupCode);
        $invitation = null;

        if ($normalizedLookup !== '') {
            $invitation = EventInvitation::query()
                ->with(['event', 'guest', 'validator', 'reentryEnabledBy', 'reentryValidator'])
                ->where(function ($query) use ($normalizedLookup): void {
                    $query
                        ->where('code', strtoupper($normalizedLookup))
                        ->orWhere('public_token', $normalizedLookup);
                })
                ->first();
        }

        return view('validator.dashboard', [
            'lookupCode' => $lookupCode,
            'invitation' => $invitation,
            'recentValidations' => EventInvitation::query()
                ->with(['event', 'guest', 'validator', 'reentryValidator'])
                ->whereNotNull('used_at')
                ->latest('used_at')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function consume(Request $request, EventInvitation $invitation): RedirectResponse
    {
        if (! $invitation->isUsed()) {
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
                ->route('validator.dashboard')
                ->with('status', 'Invitacion validada correctamente.');
        }

        if (! $invitation->hasPendingReentry()) {
            return redirect()
                ->route('validator.dashboard', ['code' => $invitation->code])
                ->with('status', 'La invitacion ya fue utilizada. Si debe volver a entrar, primero habilita un reingreso.');
        }

        $updated = EventInvitation::query()
            ->whereKey($invitation->id)
            ->whereNotNull('used_at')
            ->whereNotNull('reentry_enabled_at')
            ->whereNull('reentry_used_at')
            ->update([
                'reentry_used_at' => now(),
                'reentry_validated_by_user_id' => $request->user()->id,
                'reentry_count' => DB::raw('reentry_count + 1'),
            ]);

        if ($updated === 0) {
            return redirect()
                ->route('validator.dashboard', ['code' => $invitation->code])
                ->with('status', 'No fue posible registrar el reingreso. Recarga el codigo e intenta de nuevo.');
        }

        return redirect()
            ->route('validator.dashboard')
            ->with('status', 'Reingreso registrado correctamente.');
    }

    public function enableReentry(Request $request, EventInvitation $invitation): RedirectResponse
    {
        if (! $invitation->isUsed()) {
            return redirect()
                ->route('validator.dashboard', ['code' => $invitation->code])
                ->with('status', 'La invitacion aun no ha sido utilizada por primera vez.');
        }

        EventInvitation::query()
            ->whereKey($invitation->id)
            ->whereNotNull('used_at')
            ->update([
                'reentry_enabled_at' => now(),
                'reentry_enabled_by_user_id' => $request->user()->id,
                'reentry_used_at' => null,
                'reentry_validated_by_user_id' => null,
            ]);

        return redirect()
            ->route('validator.dashboard', ['code' => $invitation->code])
            ->with('status', 'Reingreso habilitado. La invitacion puede volver a escanearse para permitir el acceso.');
    }

    private function normalizeLookupValue(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $path = parse_url($value, PHP_URL_PATH) ?: '';
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));

            if (($segments[0] ?? null) === 'i' && isset($segments[1])) {
                return trim((string) $segments[1]);
            }
        }

        return $value;
    }
}
