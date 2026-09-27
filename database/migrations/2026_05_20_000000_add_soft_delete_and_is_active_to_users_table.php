<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // NONAKTIFKAN AKUN, JANGAN HAPUS
        // =========================
        // Hapus akun lama memakai hard delete sehingga ikut menghapus
        // laporan + riwayat status (data aduan publik) milik pelapor.
        // Soft delete mempertahankan data tersebut.
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('posisi');
            $table->softDeletes();
        });

        // Hapus unique global pada email supaya akun nonaktif tetap bisa
        // memakai email yang sama tanpa bentrok saat restore.
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique(['email', 'deleted_at']);
        });

        // =========================
        // INDEX UNTUK QUERY ADMIN
        // =========================
        // Filter status + agregat di halaman daftar laporan menyapu
        // tabel_status berulang kali. Tanpa index, tiap load list
        // melakukan full table scan.
        Schema::table('tabel_status', function (Blueprint $table) {
            $table->index('status');
            $table->index(['report_id', 'id_status']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('tabel_status', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['report_id', 'id_status']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email', 'deleted_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('is_active');
        });
    }
};
