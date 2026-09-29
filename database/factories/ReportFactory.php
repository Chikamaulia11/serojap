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
     * Nomor jalan dipakai ulang, jadi alamat dari `unique()` di sini
     * dijamin berbeda antar laporan di dalam satu proses test.
     *
     * Unik itu wajib, bukan kosmetik. `StatistikController` menghitung
     * "lokasi terpadat" dengan `groupBy('alamat')` lalu `COUNT(*)`,
     * jadi dua laporan di alamat yang sama digabung menjadi satu
     * baris dan jumlahnya keliru. Test riwayat juga memisahkan
     * laporan satu pelapor dari yang lain lewat `assertDontSee`; kalau
     * keduanya kebetulan dapat alamat identik, test itu gagal
     * karena teks yang seharusnya tidak muncul ikut tampil.
     *
     * Kolom `alamat` di database memang `text` tanpa unique index,
     * jadi jaminan ini murni di factory.
     */
    private function alamatUnik(): string
    {
        $jalan = ['Jl. Mawar', 'Jl. Melati', 'Jl. Anggrek', 'Jl. Kenanga',
            'Jl. Flamboyan', 'Jl. Cendana', 'Jl. Dahlia', 'Jl. Kenari'];

        return fake()->randomElement($jalan)
            . ' No. ' . fake()->unique()->numberBetween(1, 100000)
            . ', Baca, Purwakarta';
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->pelapor(),
            'nama_pelapor' => fake()->name(),
            'foto' => 'reports/' . fake()->uuid() . '.jpg',
            'alamat' => $this->alamatUnik(),
            'latitude' => round(fake()->latitude(-6.90, -6.50), 8),
            'longitude' => round(fake()->longitude(107.40, 107.80), 8),
            'keterangan' => fake()->paragraph(),
        ];
    }
}
