<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tabel_status') || ! Schema::hasColumn('tabel_status', 'report_id')) {
            return;
        }

        // Di MySQL, migration sebelumnya (2026_05_12_034121) me-rename
        // id_laporan -> report_id. MySQL me-rename KOLOM tapi TIDAK
        // me-rename constraint, jadi FK yang menempel masih bernama
        // `tabel_status_id_laporan_foreign`.
        //
        // Kalau di sini dipanggil dropForeign(['report_id']), Laravel
        // akan menjalankan `ALTER TABLE tabel_status DROP FOREIGN KEY
        // tabel_status_report_id_foreign` -- nama yang tidak pernah ada
        // -> MySQL error 1091 dan seluruh migrate berhenti.
        //
        // Karena itu di MySQL, langkah ini dilewati; migration
        // 2026_05_12_220000_force_fk_report_id_to_reports yang
        // mencari nama constraint sungguhan lewat information_schema
        // lalu menambahkannya ulang dengan benar.
        if (DB::connection()->getDriverName() === 'mysql') {
            return;
        }

        Schema::table('tabel_status', function (Blueprint $table) {
            $table->dropForeign(['report_id']);
        });

        Schema::table('tabel_status', function (Blueprint $table) {
            $table->foreign('report_id')
                ->references('id')
                ->on('reports')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tabel_status') || ! Schema::hasColumn('tabel_status', 'report_id')) {
            return;
        }

        if (DB::connection()->getDriverName() === 'mysql') {
            return;
        }

        Schema::table('tabel_status', function (Blueprint $table) {
            $table->dropForeign(['report_id']);
        });

        Schema::table('tabel_status', function (Blueprint $table) {
            $table->foreign('report_id')
                ->references('id_laporan')
                ->on('tabel_laporan')
                ->onDelete('cascade');
        });
    }
};