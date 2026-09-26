<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Nilai status yang sah pada tabel_status.
     */
    public const DAFTAR_STATUS = [
        'diterima',
        'diproses',
        'selesai',
        'ditolak',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->pelapor(),
            'nama_pelapor' => fake()->name(),
            'foto' => 'reports/' . fake()->uuid() . '.jpg',
            'alamat' => 'Jl. Contoh No. '
                . fake()->numberBetween(1, 200)
                . ', Baca, Purwakarta',
            'latitude' => round(fake()->latitude(-6.90, -6.50), 8),
            'longitude' => round(fake()->longitude(107.40, 107.80), 8),
            'keterangan' => fake()->paragraph(),
        ];
    }
}
