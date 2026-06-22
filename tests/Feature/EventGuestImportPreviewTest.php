<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EventGuestImportPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_detects_duplicates_in_file_and_existing_event_guests(): void
    {
        $organizer = User::factory()->create();
        $event = Event::create([
            'user_id' => $organizer->id,
            'name' => 'Cena de gala',
            'event_date' => '2026-07-25',
            'event_time' => '19:00',
            'venue' => 'Salon Principal',
            'description' => 'Prueba',
        ]);

        $event->guests()->create([
            'first_name' => 'Ana',
            'last_name' => 'Martinez',
            'phone' => '9999-1234',
            'invitation_count' => 2,
        ]);

        $file = UploadedFile::fake()->createWithContent('guests.csv', implode("\n", [
            'nombre,apellidos,numero de telefono,cantidad de invitaciones',
            'Ana,Martinez,9999-1234,2',
            'Carlos,Rivera,8888-1111,1',
            'Carlos,Rivera,7777-1111,1',
            'Lucia,Lopez,8888-1111,3',
        ]));

        $response = $this->actingAs($organizer)->post(route('events.guests.import.preview', $event), [
            'guest_file' => $file,
        ]);

        $response->assertRedirect(route('events.guests.import.create', $event, absolute: false));

        $preview = session('event_guest_import_preview_'.$event->id);

        $this->assertNotNull($preview);
        $this->assertSame(4, $preview['summary']['total']);
        $this->assertSame(0, $preview['summary']['valid']);
        $this->assertSame(4, $preview['summary']['invalid']);
    }

    public function test_execute_import_persists_rows_only_when_preview_is_clean(): void
    {
        $organizer = User::factory()->create();
        $event = Event::create([
            'user_id' => $organizer->id,
            'name' => 'Cena de gala',
            'event_date' => '2026-07-25',
            'event_time' => '19:00',
            'venue' => 'Salon Principal',
            'description' => 'Prueba',
        ]);

        $file = UploadedFile::fake()->createWithContent('guests.csv', implode("\n", [
            'nombre,apellidos,numero de telefono,cantidad de invitaciones',
            'Carlos,Rivera,8888-1111,1',
            'Lucia,Lopez,7777-1111,3',
        ]));

        $this->actingAs($organizer)->post(route('events.guests.import.preview', $event), [
            'guest_file' => $file,
        ]);

        $response = $this->actingAs($organizer)->post(route('events.guests.import.execute', $event));

        $response->assertRedirect(route('events.show', $event, absolute: false));
        $this->assertDatabaseCount('event_guests', 2);
        $this->assertDatabaseHas('event_guests', [
            'event_id' => $event->id,
            'first_name' => 'Lucia',
            'last_name' => 'Lopez',
            'phone' => '7777-1111',
            'invitation_count' => 3,
        ]);
    }
}
