<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('events.index', [
            'events' => Event::query()
                ->when(! $user->isAdmin(), fn ($query) => $query->where('user_id', $user->id))
                ->withCount('guests')
                ->orderBy('event_date')
                ->orderBy('event_time')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required', 'date_format:H:i'],
            'venue' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $event = $request->user()->events()->create($validated);

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Evento creado correctamente.');
    }

    public function show(Request $request, Event $event): View
    {
        $this->authorizeOwnership($request, $event);
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');
        $status = in_array($status, ['all', 'pending', 'partial', 'sent'], true) ? $status : 'all';
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $guestQuery = $event->guests()
            ->withCount([
                'invitations',
                'invitations as sent_invitations_count' => fn ($query) => $query->whereNotNull('sent_at'),
            ])
            ->when($status === 'pending', fn ($query) => $query->having('sent_invitations_count', '=', 0))
            ->when($status === 'partial', fn ($query) => $query->having('sent_invitations_count', '>', 0)->havingRaw('sent_invitations_count < invitations_count'))
            ->when($status === 'sent', fn ($query) => $query->havingRaw('invitations_count > 0')->havingRaw('sent_invitations_count >= invitations_count'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();

        $guestStatusSummary = $event->guests()
            ->withCount([
                'invitations',
                'invitations as sent_invitations_count' => fn ($query) => $query->whereNotNull('sent_at'),
            ])
            ->get()
            ->reduce(function (array $carry, $guest): array {
                $carry['all']++;

                $sentInvitations = (int) $guest->sent_invitations_count;
                $totalInvitations = (int) $guest->invitations_count;

                if ($sentInvitations === 0) {
                    $carry['pending']++;
                } elseif ($totalInvitations > 0 && $sentInvitations >= $totalInvitations) {
                    $carry['sent']++;
                } else {
                    $carry['partial']++;
                }

                return $carry;
            }, [
                'all' => 0,
                'pending' => 0,
                'partial' => 0,
                'sent' => 0,
            ]);

        return view('events.show', [
            'event' => $event,
            'guests' => $guestQuery,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'per_page' => $perPage,
            ],
            'guestStatusSummary' => $guestStatusSummary,
            'guestMetrics' => [
                'guests' => $event->guests()->count(),
                'invitations' => (int) $event->guests()->sum('invitation_count'),
                'sent' => $event->invitations()->whereNotNull('sent_at')->count(),
                'attendance' => $event->invitations()->whereNotNull('used_at')->count(),
            ],
        ]);
    }

    public function edit(Request $request, Event $event): View
    {
        $this->authorizeOwnership($request, $event);

        return view('events.edit', ['event' => $event]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required', 'date_format:H:i'],
            'venue' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $event->update($validated);

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Evento actualizado correctamente.');
    }

    public function destroy(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);
        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('status', 'Evento eliminado correctamente.');
    }

    private function authorizeOwnership(Request $request, Event $event): void
    {
        abort_unless($request->user()?->canAccessEvent($event), 403);
    }
}
