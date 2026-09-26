<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Laporan
        Schema::create('tabel_laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('lokasi');
            $table->string('kecamatan');
            $table->text('deskripsi');
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Status
        Schema::create('tabel_status', function (Blueprint $table) {
            $table->id('id_status');
            $table->foreignId('id_laporan')->constrained('tabel_laporan', 'id_laporan')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['diterima', 'ditolak', 'proses', 'selesai'])->default('proses');
            $table->text('keterangan')->nullable();
            $table->string('foto_perbaikan')->nullable();
            $table->timestamps();
        });

        // 3. Tabel FAQ
        Schema::create('tabel_faq', function (Blueprint $table) {
            $table->id('id_faq');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->text('jawaban')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabel_status');
        Schema::dropIfExists('tabel_laporan');
        Schema::dropIfExists('tabel_faq');
    }
};