<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'pelapor',
                'is_active' => true,
            ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Akun warga pelapor.
     */
    public function pelapor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pelapor',
        ]);
    }

    /**
     * Akun admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Akun super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'super_admin',
        ]);
    }

    /**
     * Akun yang dinonaktifkan super admin.
     *
     * `is_active` bernilai default `true` di level database, jadi
     * setiap factory tetap membuat akun yang bisa login. State ini
     * ada supaya test bisa memverifikasi perilaku `bisaLogin()` dan
     * penolakan login tanpa menuliskan atributnya manual di tiap test.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
