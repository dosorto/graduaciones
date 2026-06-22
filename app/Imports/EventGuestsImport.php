<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\EventGuest;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EventGuestsImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    public function __construct(private readonly Event $event)
    {
    }

    public function collection(Collection $rows): void
    {
        $created = 0;

        foreach ($rows as $index => $row) {
            $firstName = $this->stringValue($row, ['nombre', 'first_name', 'nombres']);
            $lastName = $this->stringValue($row, ['apellidos', 'last_name', 'apellido']);
            $phone = $this->stringValue($row, ['numero_de_telefono', 'numero_telefono', 'telefono', 'phone']);
            $invitationCount = $this->integerValue($row, ['cantidad_de_invitaciones', 'cantidad_invitaciones', 'invitaciones'], 1);

            if ($firstName === '' && $lastName === '' && $phone === '') {
                continue;
            }

            if ($firstName === '' || $lastName === '' || $phone === '' || $invitationCount < 1) {
                throw ValidationException::withMessages([
                    'guest_file' => 'El archivo contiene filas incompletas o cantidades invalidas. Revise la fila '.($index + 2).'.',
                ]);
            }

            EventGuest::create([
                'event_id' => $this->event->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'invitation_count' => $invitationCount,
            ]);

            $created++;
        }

        if ($created === 0) {
            throw ValidationException::withMessages([
                'guest_file' => 'El archivo no contiene invitados validos para importar.',
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
}
