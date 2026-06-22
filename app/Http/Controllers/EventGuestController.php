<?php

namespace App\Http\Controllers;

use App\Exports\EventGuestsTemplateExport;
use App\Imports\EventGuestsImport;
use App\Models\Event;
use App\Models\EventGuest;
use App\Services\EventGuestImportPreviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EventGuestController extends Controller
{
    public function createImport(Request $request, Event $event): View
    {
        $this->authorizeOwnership($request, $event);

        $preview = $request->session()->get($this->previewSessionKey($event), [
            'rows' => [],
            'summary' => ['total' => 0, 'valid' => 0, 'invalid' => 0],
        ]);

        $search = trim((string) $request->query('search', ''));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
        $page = max(1, (int) $request->query('page', 1));

        $filteredRows = collect($preview['rows'])
            ->filter(function (array $row) use ($search) {
                if ($search === '') {
                    return true;
                }

                $haystack = strtolower("{$row['first_name']} {$row['last_name']}");

                return str_contains($haystack, strtolower($search));
            })
            ->values();

        $paginatedRows = new LengthAwarePaginator(
            $filteredRows->forPage($page, $perPage)->values(),
            $filteredRows->count(),
            $perPage,
            $page,
            [
                'path' => route('events.guests.import.create', $event),
                'query' => $request->query(),
            ]
        );

        return view('events.import-guests', [
            'event' => $event,
            'preview' => $preview,
            'rows' => $paginatedRows,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function previewImport(Request $request, Event $event, EventGuestImportPreviewService $previewService): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);

        $request->validate([
            'guest_file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        $preview = $previewService->buildPreview($event, $request->file('guest_file'));

        $request->session()->put($this->previewSessionKey($event), $preview);

        return redirect()
            ->route('events.guests.import.create', $event)
            ->with('status', 'Archivo cargado. Revisa la validacion antes de ejecutar la importacion.');
    }

    public function executeImport(Request $request, Event $event, EventGuestImportPreviewService $previewService): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);

        $preview = $request->session()->get($this->previewSessionKey($event));
        abort_unless(is_array($preview), 422, 'No hay un archivo preparado para importar.');

        $previewService->persistPreview($event, $preview);
        $request->session()->forget($this->previewSessionKey($event));

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Invitados importados correctamente.');
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'invitation_count' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $event->guests()->create($validated);

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Invitado agregado correctamente.');
    }

    public function destroy(Request $request, Event $event, EventGuest $guest): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);
        abort_unless($guest->event_id === $event->id, 404);

        $guest->delete();

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Invitado eliminado correctamente.');
    }

    public function import(Request $request, Event $event): RedirectResponse
    {
        $this->authorizeOwnership($request, $event);

        $request->validate([
            'guest_file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new EventGuestsImport($event), $request->file('guest_file'));

        return redirect()
            ->route('events.show', $event)
            ->with('status', 'Invitados importados correctamente.');
    }

    public function template()
    {
        return Excel::download(new EventGuestsTemplateExport(), 'plantilla-invitados-evento.xlsx');
    }

    private function authorizeOwnership(Request $request, Event $event): void
    {
        abort_unless($event->user_id === $request->user()->id, 403);
    }

    private function previewSessionKey(Event $event): string
    {
        return 'event_guest_import_preview_'.$event->id;
    }
}
