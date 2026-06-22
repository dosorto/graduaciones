<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class EventGuestsTemplateExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'nombre',
            'apellidos',
            'numero de telefono',
            'cantidad de invitaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['Ana', 'Martinez', '9999-1234', 2],
            ['Carlos', 'Rivera', '+504 9876-5432', 4],
        ];
    }

    public function title(): string
    {
        return 'Invitados';
    }
}
