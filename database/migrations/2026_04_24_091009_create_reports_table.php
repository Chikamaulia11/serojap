<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('reports', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->string('nama_pelapor');
        $table->string('foto')->nullable();
        $table->text('alamat');

        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);

        $table->text('keterangan');

        $table->enum('status', ['diterima','diproses','selesai'])
              ->default('diterima');

        $table->timestamps();
    });
}
};
