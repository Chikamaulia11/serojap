<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * Pendaftaran langsung membawa user masuk.
     *
     * Test bawaan Breeze masih mengharapkan redirect ke halaman
     * login dan `assertGuest()`, padahal `RegisteredUserController`
     * memang melakukan `Auth::login($user)` lalu mengarahkan ke
     * dashboard dengan flash "Registrasi berhasil". Test lama itu
     * selalu gagal dan tidak pernah diubah.
     */
    public function test_new_users_can_register_and_are_logged_in(): void
    {
        Event::fake([Registered::class]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $response->assertSessionHas('success');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'pelapor',
        ]);
    }

    public function test_registration_emits_the_registered_event(): void
    {
        Event::fake([Registered::class]);

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Event::assertDispatched(Registered::class);
    }

    public function test_email_is_stored_lowercase_and_trimmed(): void
    {
        $this->post('/register', [
            'name' => '  Test User  ',
            'email' => '  MiXeD@Example.COM ',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'mixed@example.com',
            'name' => 'Test User',
        ]);
    }

    public function test_an_existing_active_email_cannot_register_again(): void
    {
        User::factory()->create(['email' => 'udahada@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'udahada@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'udahada@example.com')->count());
    }

    /**
     * Email milik akun yang sudah di-soft-delete harus ditolak.
     *
     * Kolom `email` tidak lagi punya unique global; yang ada hanya
     * `unique(['email', 'deleted_at'])`, dan `NULL` pada unique index
     * MySQL tidak dianggap duplikat. Jadi kalau email yang sama
     * diizinkan lagi, baris baru tersimpan dengan `deleted_at = NULL`
     * berdampingan dengan akun lama -- dan `Auth::attempt()` akan
     * menemukan akun lama lebih dulu sehingga user yang baru daftar
     * selalu ditolak sebagai "akun dinonaktifkan".
     *
     * Aplikasi karena itu wajib menolak lebih dulu, di sini.
     */
    public function test_email_of_a_deactivated_account_cannot_register_again(): void
    {
        User::factory()->nonaktif()->create([
            'email' => 'nonaktif@example.com',
        ])->delete();

        $this->assertSoftDeleted('users', ['email' => 'nonaktif@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'nonaktif@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Tidak boleh ada baris baru dengan email yang sama dan
        // deleted_at NULL.
        $this->assertSame(0, User::where('email', 'nonaktif@example.com')->count());
    }

    /**
     * Pencocokan email harus tahan huruf besar, karena database
     * MySQL bisa saja dikonfigurasi case-insensitive atau tidak.
     */
    public function test_email_comparison_is_case_insensitive(): void
    {
        User::factory()->create(['email' => 'kapital@Example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'kapital@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_must_be_confirmed(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'beda-sekali',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
