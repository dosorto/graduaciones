<?php

namespace App\Services;

use App\Imports\EventGuestsPreviewImport;
use App\Models\Event;
use App\Models\EventGuest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EventGuestImportPreviewService
{
    public function buildPreview(Event $event, UploadedFile $file): array
    {
        $import = new EventGuestsPreviewImport();
        Excel::import($import, $file);

        $rows = $import->rows;

        $existingGuests = $event->guests()->get(['first_name', 'last_name', 'phone']);
        $existingNames = $existingGuests
            ->map(fn (EventGuest $guest) => $this->normalizedName($guest->first_name, $guest->last_name))
            ->filter()
            ->values();
        $existingPhones = $existingGuests
            ->map(fn (EventGuest $guest) => $this->normalizedPhone($guest->phone))
            ->filter()
            ->values();

        $nameCounts = [];
        $phoneCounts = [];
        $preparedRows = [];

        foreach ($rows as $index => $row) {
            $firstName = $this->stringValue($row, ['nombre', 'first_name', 'nombres']);
            $lastName = $this->stringValue($row, ['apellidos', 'last_name', 'apellido']);
            $phone = $this->stringValue($row, ['numero_de_telefono', 'numero_telefono', 'telefono', 'phone']);
            $invitationCount = $this->integerValue($row, ['cantidad_de_invitaciones', 'cantidad_invitaciones', 'invitaciones'], 1);

            if ($firstName === '' && $lastName === '' && $phone === '') {
                continue;
            }

            $normalizedName = $this->normalizedName($firstName, $lastName);
            $normalizedPhone = $this->normalizedPhone($phone);

            if ($normalizedName !== '') {
                $nameCounts[$normalizedName] = ($nameCounts[$normalizedName] ?? 0) + 1;
            }

            if ($normalizedPhone !== '') {
                $phoneCounts[$normalizedPhone] = ($phoneCounts[$normalizedPhone] ?? 0) + 1;
            }

            $preparedRows[] = [
                'row_number' => $index + 2,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'invitation_count' => $invitationCount,
                'normalized_name' => $normalizedName,
                'normalized_phone' => $normalizedPhone,
            ];
        }

        $previewRows = collect($preparedRows)->map(function (array $row) use ($nameCounts, $phoneCounts, $existingNames, $existingPhones) {
            $errors = [];

            if ($row['first_name'] === '' || $row['last_name'] === '' || $row['phone'] === '') {
                $errors[] = 'Faltan campos obligatorios.';
            }

            if ($row['invitation_count'] < 1 || $row['invitation_count'] > 20) {
                $errors[] = 'La cantidad de invitaciones debe estar entre 1 y 20.';
            }

            if ($row['normalized_name'] !== '' && ($nameCounts[$row['normalized_name']] ?? 0) > 1) {
                $errors[] = 'Nombre duplicado dentro del archivo.';
            }

            if ($row['normalized_phone'] !== '' && ($phoneCounts[$row['normalized_phone']] ?? 0) > 1) {
                $errors[] = 'Telefono duplicado dentro del archivo.';
            }

            if ($row['normalized_name'] !== '' && $existingNames->contains($row['normalized_name'])) {
                $errors[] = 'Nombre ya existe en este evento.';
            }

            if ($row['normalized_phone'] !== '' && $existingPhones->contains($row['normalized_phone'])) {
                $errors[] = 'Telefono ya existe en este evento.';
            }

            return [
                'row_number' => $row['row_number'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'phone' => $row['phone'],
                'invitation_count' => $row['invitation_count'],
                'errors' => $errors,
                'valid' => count($errors) === 0,
            ];
        })->values();

        return [
            'rows' => $previewRows->all(),
            'summary' => [
                'total' => $previewRows->count(),
                'valid' => $previewRows->where('valid', true)->count(),
                'invalid' => $previewRows->where('valid', false)->count(),
            ],
        ];
    }

    public function persistPreview(Event $event, array $preview): void
    {
        $rows = collect($preview['rows'] ?? []);

        if ($rows->isEmpty() || $rows->contains(fn (array $row) => ! $row['valid'])) {
            abort(422, 'La importacion no puede ejecutarse mientras existan errores.');
        }

        foreach ($rows as $row) {
            $event->guests()->create([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'phone' => $row['phone'],
                'invitation_count' => $row['invitation_count'],
            ]);
        }
    }

    private function stringValue(Collection $row, array $keys): string
    {
        foreach ($keys as $key) {
            $value = $row->get($key);

            if ($value !== null) {
                return trim((string) $value);
            }
        }

        return '';
    }

    private function integerValue(Collection $row, array $keys, int $default): int
    {
        foreach ($keys as $key) {
            $value = $row->get($key);

            if ($value !== null && $value !== '') {
                return (int) $value;
            }
        }

        return $default;
    }

    private function normalizedName(string $firstName, string $lastName): string
    {
        return Str::of($firstName.' '.$lastName)
            ->squish()
            ->lower()
            ->value();
    }

    private function normalizedPhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?: '';
    }
}
