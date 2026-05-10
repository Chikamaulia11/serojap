<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('foto_profil')->nullable()->after('password');
            $table->enum('role', ['pelapor', 'admin', 'super_admin'])->default('pelapor')->after('foto_profil');
        });
    }

    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn(['foto_profil', 'role']);
        });
    }
};
