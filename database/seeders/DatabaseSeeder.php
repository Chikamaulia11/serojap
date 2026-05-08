<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@serojap.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@serojap.com',
                'password' => bcrypt('password'), // Ubah password di sini
                'role' => 'super_admin',
                'posisi' => 'Administrator Utama',
                'email_verified_at' => now(),
            ]
        );

        // Admin biasa
        User::updateOrCreate(
            ['email' => 'admin2@serojap.com'],
            [
                'name' => 'Admin Teknik',
                'email' => 'admin2@serojap.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'posisi' => 'Petugas Lapangan',
                'email_verified_at' => now(),
            ]
        );

        // Pelapor contoh
        User::updateOrCreate(
            ['email' => 'pelapor@serojap.com'],
            [
                'name' => 'Warga Purwakarta',
                'email' => 'pelapor@serojap.com',
                'password' => bcrypt('password'),
                'role' => 'pelapor',
                'posisi' => 'Warga',
                'email_verified_at' => now(),
            ]
        );

        // Test user
        User::factory(5)->create([
            'role' => 'pelapor',
            'email_verified_at' => now(),
        ]);
    }
}
