<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\TabelStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Laporan contoh untuk pengembangan lokal.
 *
 * Bertujuan agar dashboard admin, daftar laporan, dan grafik statistik punya
 * data saat pertama kali dijalankan. Jangan jalankan seeder ini di production.
 */
class DemoReportSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor = User::where('email', 'pelapor@serojap.test')->first();
        $admin = User::where('email', 'admin@serojap.test')->first();

        if (! $pelapor || ! $admin) {
            $this->command?->warn('Akun demo tidak ada, jalankan DemoUserSeeder dulu.');

            return;
        }

        $contoh = [
            ['5', 'Asep Saepudin', 'Jl. Raya No. 12, Sokaraja', -6.55802, 107.49100, 'Aspal berlubang lebar di depan sekolah.', ['diterima', 'diproses', 'selesai']],
            ['4', 'Rina Marlina', 'Gg. Melati No. 4, Cigirip', -6.56200, 107.50200, 'Jalan penghubung kampung rusak berat.', ['diterima', 'diproses']],
            ['3', 'Budi Santoso', 'Jl. Adi Sucipto No. 88, Cipanengah', -6.57200, 107.51800, 'Trotoar hilang dan jalan berlubang besar.', ['diterima', 'selesai']],
            ['2', 'Dewi Lestari', 'Jl. Merdeka No. 17, Kranchi', -6.54500, 107.48900, 'Marka jalan hilang sehingga berbahaya.', ['diterima']],
            ['1', 'Agus Setiawan', 'Jl. Raya Babelan No. 3, Songgar', -6.58100, 107.47500, 'Lubang besar di tikungan sharp', ['diterima', 'ditolak']],
        ];

        foreach ($contoh as $row) {
            $laporan = Report::create([
                'user_id' => $pelapor->id,
                'nama_pelapor' => $row[1],
                'foto' => null,
                'alamat' => $row[2].', Purwakarta',
                'latitude' => $row[3],
                'longitude' => $row[4],
                'keterangan' => $row[5],
            ]);

            $laporan->forceFill(['created_at' => now()->subMonths((int) $row[0]), 'updated_at' => now()->subMonths((int) $row[0])])->save();

            foreach ($row[6] as $index => $status) {
                $riwayat = TabelStatus::create([
                    'report_id' => $laporan->id,
                    'user_id' => $admin->id,
                    'status' => $status,
                    'keterangan' => 'Contoh riwayat status: '.$status.'.',
                    'foto_perbaikan' => null,
                ]);

                $riwayat->forceFill([
                    'created_at' => now()->subMonths((int) $row[0])->addDays($index * 3),
                    'updated_at' => now()->subMonths((int) $row[0])->addDays($index * 3),
                ])->save();
            }
        }
    }
}
