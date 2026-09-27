<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun demo untuk pengembangan lokal.
 *
 * Jalankan `php artisan migrate:fresh --seed` pada environment lokal.
 *
 * Seeder ini menolak jalan di production dan TIDAK pernah menimpa
 * password akun yang sudah ada. Kedua hal sama pentingnya: kalau tidak,
 * satu `db:seed` di server akan me-reset password super admin yang sedang
 * dipakai menjadi string publik "password".
 */
class DemoUserSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, email: string, role: string, posisi: ?string}>
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
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('DemoUserSeeder dilewati: environment bukan local/testing.');

            return;
        }

        foreach (self::AKUN as $akun) {
            $ada = User::withTrashed()
                ->where('email', $akun['email'])
                ->exists();

            if ($ada) {
                $this->command?->line('  - lewati ' . $akun['email'] . ' (akun sudah ada)');

                continue;
            }

            $user = User::withRole($akun['role'], [
                'name' => $akun['name'],
                'email' => $akun['email'],
                'password' => 'password',
                'posisi' => $akun['posisi'],
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();

            $this->command?->line('  + buat ' . $akun['email']);
        }
    }
}
