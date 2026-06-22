<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $events = $user->events()
            ->withCount('guests')
            ->with('guests')
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->get();

        $today = Carbon::today();
        $nextEvent = $events
            ->filter(fn ($event) => $event->event_date !== null && $event->event_date->greaterThanOrEqualTo($today))
            ->sortBy(fn ($event) => $event->event_date->format('Y-m-d').'-'.optional($event->event_time)->format('H:i'))
            ->first() ?? $events->first();

        return view('dashboard', [
            'events' => $events,
            'metrics' => [
                'events' => $events->count(),
                'guests' => $events->sum('guests_count'),
                'invitations' => $events->sum(fn ($event) => $event->guests->sum('invitation_count')),
                'nextEvent' => $nextEvent,
            ],
        ]);
    }
}
