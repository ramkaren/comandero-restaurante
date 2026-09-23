<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_login_requires_valid_credentials_fields(): void
    {
        $response = $this->post(route('login.store'), []);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    public function test_user_is_redirected_to_the_role_dashboard_after_login(): void
    {
        Role::create(['name' => 'mesero', 'guard_name' => 'web']);
        $user = User::factory()->create([
            'email' => 'mesero@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('mesero');

        $response = $this->post(route('login.store'), [
            'email' => 'mesero@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard del mesero')
            ->assertSee($user->name)
            ->assertSee('Rol: mesero');
    }

    public function test_user_without_a_role_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertForbidden();
    }

    public function test_authenticated_user_can_logout(): void
    {
        Role::create(['name' => 'cajero', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('cajero');
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
