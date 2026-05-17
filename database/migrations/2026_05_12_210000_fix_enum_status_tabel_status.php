<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pindahkan data lama yang bernilai 'proses' menjadi 'diproses'
        DB::table('tabel_status')
            ->where('status', 'proses')
            ->update(['status' => 'diproses']);

        // Samakan enum dengan validasi di controller dan dropdown UI
        DB::statement("
            ALTER TABLE tabel_status
            MODIFY status ENUM('diterima', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'diproses'
        ");
    }

    public function down(): void
    {
        // Kembalikan data 'diproses' ke 'proses' (untuk rollback sederhana)
        DB::table('tabel_status')
            ->where('status', 'diproses')
            ->update(['status' => 'proses']);

        DB::statement("
            ALTER TABLE tabel_status
            MODIFY status ENUM('diterima', 'proses', 'selesai', 'ditolak') NOT NULL DEFAULT 'proses'
        ");
    }
};

