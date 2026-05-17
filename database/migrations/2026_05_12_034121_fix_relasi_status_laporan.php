<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // HAPUS STATUS DARI REPORTS
        // =========================
        Schema::table('reports', function (Blueprint $table) {

            if (Schema::hasColumn('reports', 'status')) {
                $table->dropColumn('status');
            }

        });

        // =========================
        // UBAH id_laporan -> report_id
        // =========================
        Schema::table('tabel_status', function (Blueprint $table) {

            if (Schema::hasColumn('tabel_status', 'id_laporan')) {
                $table->renameColumn('id_laporan', 'report_id');
            }

        });
    }

    public function down(): void
    {
        // =========================
        // BALIKIN STATUS
        // =========================
        Schema::table('reports', function (Blueprint $table) {

            $table->string('status')
                ->default('diterima');

        });

        // =========================
        // BALIKIN report_id
        // =========================
        Schema::table('tabel_status', function (Blueprint $table) {

            if (Schema::hasColumn('tabel_status', 'report_id')) {
                $table->renameColumn('report_id', 'id_laporan');
            }

        });
    }
};