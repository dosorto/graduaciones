<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_management_screen(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk();
    }

    public function test_non_admin_cannot_view_user_management_screen(): void
    {
        $organizer = User::factory()->create(['role' => 'organizer']);

        $this->actingAs($organizer)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_update_other_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'organizer']);

        $this->actingAs($admin)
            ->put(route('users.update-role', $user), ['role' => 'validator'])
            ->assertRedirect();

        $this->assertSame('validator', $user->fresh()->role);
    }

    public function test_last_admin_cannot_demote_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('users.index'))
            ->put(route('users.update-role', $admin), ['role' => 'organizer'])
            ->assertRedirect(route('users.index', absolute: false));

        $this->assertSame('admin', $admin->fresh()->role);
    }
}
