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
        return view('events.index', [
            'events' => $request->user()
                ->events()
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
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $guests = $event->guests()
            ->withCount('invitations')
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

        return view('events.show', [
            'event' => $event,
            'guests' => $guests,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
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
        abort_unless($event->user_id === $request->user()->id, 403);
    }
}
