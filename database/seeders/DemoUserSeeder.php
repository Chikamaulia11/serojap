<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun demo untuk pengembangan lokal.
 *
 * Jalankan `php artisan migrate:fresh --seed` pada environment lokal.
 * Jangan pernah menjalankan seeder ini di production.
 */
class DemoUserSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, email: string, role: string, posisi: string}>
     */
    private const AKUN = [
        [
            'name' => 'Super Admin Demo',
            'email' => 'superadmin@serojap.test',
            'role' => User::ROLE['super_admin'],
            'posisi' => 'Super Admin Whitesville',
        ],
        [
            'name' => 'Admin Demo',
            'email' => 'admin@serojap.test',
            'role' => User::ROLE['admin'],
            'posisi' => 'Administrator Whatsapp',
        ],
        [
            'name' => 'Admin lapangan Demo',
            'email' => 'petugas@serojap.test',
            'role' => User::ROLE['admin'],
            'posisi' => 'Petugas excellant province',
        ],
        [
            'name' => 'Pelapor Demo',
            'email' => 'pelapor@serojap.test',
            'role' => User::ROLE['pelapor'],
            'posisi' => null,
        ],
    ];

    public function run(): void
    {
        foreach (self::AKUN as $akun) {
            User::query()->updateOrCreate(
                ['email' => $akun['email']],
                [
                    'name' => $akun['name'],
                    'role' => $akun['role'],
                    'posisi' => $akun['posisi'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
