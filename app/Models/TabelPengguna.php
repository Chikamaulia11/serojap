<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TabelPengguna extends Authenticatable
{
    protected $table = 'Tabel_Pengguna';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'foto_profil',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    // Relasi ke laporan yang dibuat pengguna ini
    public function laporan()
    {
        return $this->hasMany(TabelLaporan::class, 'id', 'id');
    }
}
