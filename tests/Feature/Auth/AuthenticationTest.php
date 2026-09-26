<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_admin_can_authenticate_using_the_admin_login_screen(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post('/login/admin', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_super_admin_can_authenticate_using_the_super_admin_login_screen(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->post('/login/superadmin', [
            'email' => $superAdmin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($superAdmin);
        $response->assertRedirect(route('superadmin.dashboard', absolute: false));
    }

    public function test_pelapor_cannot_log_in_through_the_admin_login_screen(): void
    {
        $pelapor = User::factory()->pelapor()->create();

        $response = $this->from('/login/admin')
            ->post('/login/admin', [
                'email' => $pelapor->email,
                'password' => 'password',
            ]);

        $this->assertGuest();
        $response->assertRedirect('/login/admin');
        $response->assertSessionHasErrors([
            'email' => 'Akun ini bukan akun admin.',
        ]);
    }

    public function test_admin_cannot_log_in_through_the_pelapor_login_screen(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->from('/login')
            ->post('/login', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Akun ini bukan akun pelapor.',
        ]);
    }

    public function test_pelapor_cannot_log_in_through_the_super_admin_login_screen(): void
    {
        $pelapor = User::factory()->pelapor()->create();

        $response = $this->from('/login/superadmin')
            ->post('/login/superadmin', [
                'email' => $pelapor->email,
                'password' => 'password',
            ]);

        $this->assertGuest();
        $response->assertRedirect('/login/superadmin');
        $response->assertSessionHasErrors([
            'email' => 'Akun ini bukan akun super admin.',
        ]);
    }

    public function test_admin_cannot_log_in_through_the_super_admin_login_screen(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->from('/login/superadmin')
            ->post('/login/superadmin', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $this->assertGuest();
        $response->assertRedirect('/login/superadmin');
        $response->assertSessionHasErrors([
            'email' => 'Akun ini bukan akun super admin.',
        ]);
    }

    public function test_email_yang_gagal_login_kemudian_ada_di_input_kembali(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->from('/login')
            ->post('/login', [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $response->assertSessionHasInput('email', $admin->email);
    }
}
