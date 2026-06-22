<?php

namespace Tests\Feature;

use App\Imports\EventGuestsImport;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EventGuestsImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_rows_using_template_headings_with_spaces(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'name' => 'Cena de gala',
            'event_date' => '2026-07-25',
            'event_time' => '19:00',
            'venue' => 'Salon Principal',
            'description' => 'Prueba de importacion',
        ]);

        $import = new EventGuestsImport($event);

        $import->collection(new Collection([
            new Collection([
                'nombre' => 'Ana',
                'apellidos' => 'Martinez',
                'numero_de_telefono' => '9999-1234',
                'cantidad_de_invitaciones' => 2,
            ]),
        ]));

        $this->assertDatabaseHas('event_guests', [
            'event_id' => $event->id,
            'first_name' => 'Ana',
            'last_name' => 'Martinez',
            'phone' => '9999-1234',
            'invitation_count' => 2,
        ]);
    }

    public function test_it_imports_rows_using_legacy_underscore_headings(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'name' => 'Cena de gala',
            'event_date' => '2026-07-25',
            'event_time' => '19:00',
            'venue' => 'Salon Principal',
            'description' => 'Prueba de importacion',
        ]);

        $import = new EventGuestsImport($event);

        $import->collection(new Collection([
            new Collection([
                'nombre' => 'Carlos',
                'apellidos' => 'Rivera',
                'numero_telefono' => '+504 9876-5432',
                'cantidad_invitaciones' => 4,
            ]),
        ]));

        $this->assertDatabaseHas('event_guests', [
            'event_id' => $event->id,
            'first_name' => 'Carlos',
            'last_name' => 'Rivera',
            'phone' => '+504 9876-5432',
            'invitation_count' => 4,
        ]);
    }
}
