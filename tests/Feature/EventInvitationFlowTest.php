<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\EventInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventInvitationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_creation_generates_one_invitation_per_assigned_slot(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'name' => 'Cena de gala',
            'event_date' => '2026-07-25',
            'event_time' => '19:00',
            'venue' => 'Salon Principal',
            'description' => 'Prueba',
        ]);

        $guest = EventGuest::create([
            'event_id' => $event->id,
            'first_name' => 'Ana',
            'last_name' => 'Martinez',
            'phone' => '9999-1234',
            'invitation_count' => 5,
        ]);

        $this->assertCount(5, $guest->fresh()->invitations);
        $this->assertDatabaseCount('event_invitations', 5);
    }

    public function test_validator_can_consume_an_invitation_once(): void
    {
        $validator = User::factory()->create(['role' => 'validator']);
        $organizer = User::factory()->create();
        $event = Event::create([
            'user_id' => $organizer->id,
            'name' => 'Cena de gala',
            'event_date' => '2026-07-25',
            'event_time' => '19:00',
            'venue' => 'Salon Principal',
            'description' => 'Prueba',
        ]);

        $guest = EventGuest::create([
            'event_id' => $event->id,
            'first_name' => 'Carlos',
            'last_name' => 'Rivera',
            'phone' => '9999-1234',
            'invitation_count' => 1,
        ]);

        $invitation = $guest->fresh()->invitations()->firstOrFail();

        $response = $this->actingAs($validator)->post(route('validator.invitations.consume', $invitation));

        $response->assertRedirect(route('validator.dashboard', ['code' => $invitation->code], absolute: false));
        $this->assertNotNull($invitation->fresh()->used_at);
        $this->assertSame($validator->id, $invitation->fresh()->validated_by_user_id);
    }

    public function test_non_validator_cannot_access_validator_module(): void
    {
        $user = User::factory()->create(['role' => 'organizer']);

        $this->actingAs($user)
            ->get(route('validator.dashboard'))
            ->assertForbidden();
    }

    public function test_public_invitation_page_can_be_rendered(): void
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

        $guest = EventGuest::create([
            'event_id' => $event->id,
            'first_name' => 'Lucia',
            'last_name' => 'Lopez',
            'phone' => '9999-1234',
            'invitation_count' => 1,
        ]);

        $invitation = $guest->fresh()->invitations()->firstOrFail();

        $this->get(route('invitations.public.show', $invitation->public_token))
            ->assertOk()
            ->assertSee($event->name)
            ->assertSee($guest->full_name)
            ->assertSee($invitation->code);
    }

    public function test_public_invitation_image_can_be_rendered(): void
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

        $guest = EventGuest::create([
            'event_id' => $event->id,
            'first_name' => 'Laura',
            'last_name' => 'Molina',
            'phone' => '9999-1234',
            'invitation_count' => 1,
        ]);

        $invitation = $guest->fresh()->invitations()->firstOrFail();

        $this->get(route('invitations.public.image', $invitation->public_token))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_organizer_can_share_single_invitation_and_mark_it_as_sent(): void
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

        $guest = EventGuest::create([
            'event_id' => $event->id,
            'first_name' => 'Marta',
            'last_name' => 'Ramos',
            'phone' => '9999-1234',
            'invitation_count' => 2,
        ]);

        $invitation = $guest->fresh()->invitations()->firstOrFail();

        $response = $this->actingAs($organizer)->post(route('events.guests.invitations.share', [$event, $guest, $invitation]));

        $response->assertRedirectContains('https://wa.me/');
        $response->assertRedirectContains(rawurlencode($invitation->publicUrl()));
        $this->assertNotNull($invitation->fresh()->sent_at);
        $this->assertNull($guest->fresh()->invitations()->whereKeyNot($invitation->id)->first()->sent_at);
    }

    public function test_organizer_can_add_one_invitation_from_guest_management(): void
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

        $guest = EventGuest::create([
            'event_id' => $event->id,
            'first_name' => 'Jose',
            'last_name' => 'Pineda',
            'phone' => '9999-1234',
            'invitation_count' => 1,
        ]);

        $this->actingAs($organizer)
            ->post(route('events.guests.invitations.add', [$event, $guest]))
            ->assertRedirect(route('events.guests.invitations.show', [$event, $guest], absolute: false));

        $this->assertSame(2, $guest->fresh()->invitation_count);
        $this->assertCount(2, $guest->fresh()->invitations);
    }

    public function test_organizer_can_delete_unused_invitation_from_guest_management(): void
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

        $guest = EventGuest::create([
            'event_id' => $event->id,
            'first_name' => 'Paola',
            'last_name' => 'Rivas',
            'phone' => '9999-1234',
            'invitation_count' => 2,
        ]);

        $invitation = $guest->fresh()->invitations()->orderByDesc('sequence_number')->firstOrFail();

        $this->actingAs($organizer)
            ->delete(route('events.guests.invitations.destroy', [$event, $guest, $invitation]))
            ->assertRedirect(route('events.guests.invitations.show', [$event, $guest], absolute: false));

        $this->assertSame(1, $guest->fresh()->invitation_count);
        $this->assertCount(1, $guest->fresh()->invitations);
    }
}
