<?php

namespace Database\Seeders;

use App\Models\TabelFaq;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * FAQ contoh untuk pengembangan lokal.
 *
 * Data ini dipakai supaya halaman Pusat Bantuan dan menu Kelola FAQ pada
 * admin punya isi. Jangan jalankan seeder ini di production.
 */
class DemoFaqSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@serojap.test')->first();

        if (! $admin) {
            $this->command?->warn('Akun admin demo tidak ada, jalankan DemoUserSeeder dulu.');

            return;
        }

        $faq = [
            [
                'pertanyaan' => 'Bagaimana cara mengirim laporan kerusakan jalan?',
                'jawaban' => "Masuk ke menu Laporan pada dashboard, isi nama, alamat lengkap lokasi, keterangan kerusakan, lalu pilih titik lokasi pada peta. Laporan tidak dapat dikirim tanpa foto pendukung.",
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Berapa lama laporan saya diproses?',
                'jawaban' => 'Admin memverifikasi laporan pada jam kerja. Status laporan akan berubah dari Diterima menjadi Diproses lalu Selesai. Anda dapat memantau perkembangannya melalui menu Riwayat.',
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Apakah saya bisa melacak status laporan saya?',
                'jawaban' => 'Bisa. Buka menu Riwayat untuk melihat seluruh laporan beserta status terbaru dan foto hasil perbaikan bila sudah selesai.',
                'urutan' => 3,
            ],
            [
                'pertanyaan' => 'Mengapa laporan saya ditolak?',
                'jawaban' => 'Laporan ditolak apabila lokasi tidak dapat diverifikasi, keterangan tidak jelas, atau bukan kerusakan jalan yang menjadi tanggung jawab pemerintah daerah. Mohon perbaiki data dan kirim ulang.',
                'urutan' => 4,
            ],
            [
                'pertanyaan' => 'Apakah satu akun boleh mengirim banyak laporan?',
                'jawaban' => 'Boleh. Tidak ada batas jumlah laporan per akun, selama setiap laporan mewakili lokasi kerusakan yang berbeda.',
                'urutan' => 5,
            ],
        ];

        foreach ($faq as $row) {
            TabelFaq::updateOrCreate(
                ['pertanyaan' => $row['pertanyaan']],
                [
                    'user_id' => $admin->id,
                    'jawaban' => $row['jawaban'],
                    'urutan' => $row['urutan'],
                ],
            );
        }
    }
}
