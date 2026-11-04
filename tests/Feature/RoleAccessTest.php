<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role).' '.uniqid(),
            'email' => $role.'-'.uniqid().'@costumerent.local',
            'password' => bcrypt('password123'),
            'role' => $role,
        ]);
    }

    public function test_customer_is_blocked_from_admin_dashboard(): void
    {
        $response = $this->actingAs($this->user('customer'))
            ->get(route('admin.dashboard'));

        // The role middleware either aborts 403 or redirects a customer away.
        $this->assertTrue(
            $response->isForbidden() || $response->isRedirect(),
            'Customer should be blocked from the admin dashboard.'
        );
        $this->assertFalse($response->isOk());
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->user('admin'))
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_staff_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->user('staff'))
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }
}
