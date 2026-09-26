<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pindahkan data lama yang bernilai 'proses' menjadi 'diproses'
        $this->pindahkanNilaiLama('proses', 'diproses');

        // Samakan daftar status dengan validasi di controller dan dropdown UI
        $this->timpaEnumStatus(
            ['diterima', 'diproses', 'selesai', 'ditolak'],
            'diproses'
        );
    }

    public function down(): void
    {
        // Kembalikan data 'diproses' ke 'proses' (untuk rollback sederhana)
        $this->pindahkanNilaiLama('diproses', 'proses');

        $this->timpaEnumStatus(
            ['diterima', 'proses', 'selesai', 'ditolak'],
            'proses'
        );
    }

    /**
     * Ubah daftar nilai yang diizinkan pada kolom status.
     *
     * Di MySQL/MariaDB enum dibuat lewat ALTER TABLE ... MODIFY, sama seperti
     * perilaku asli migration ini. Di driver lain (SQLite untuk test & CI)
     * enum tidak didukung, sehingga kolomnya diubah menjadi string biasa —
     * daftar nilai tetap divalidasi di level aplikasi.
     */
    private function timpaEnumStatus(array $daftarStatus, string $default): void
    {
        if (! Schema::hasTable('tabel_status') || ! Schema::hasColumn('tabel_status', 'status')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            $daftarSql = implode(', ', array_map(
                fn (string $status) => "'" . $status . "'",
                $daftarStatus
            ));

            DB::statement(
                "ALTER TABLE tabel_status MODIFY status ENUM({$daftarSql}) NOT NULL DEFAULT '{$default}'"
            );

            return;
        }

        Schema::table('tabel_status', function (Blueprint $table) use ($default) {
            $table->string('status')->default($default)->change();
        });
    }

    private function pindahkanNilaiLama(string $dari, string $ke): void
    {
        if (! Schema::hasTable('tabel_status') || ! Schema::hasColumn('tabel_status', 'status')) {
            return;
        }

        DB::table('tabel_status')
            ->where('status', $dari)
            ->update(['status' => $ke]);
    }
};
