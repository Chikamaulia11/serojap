<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelLaporan extends Model
{
    protected $table = 'tabel_laporan';
    protected $primaryKey = 'id_laporan';
    
    protected $fillable = [
        'user_id',
        'lokasi',
        'kecamatan',
        'deskripsi',
        'foto',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relasi ke User (pelapor)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Relasi ke Tabel_Status (bisa banyak riwayat status)
    public function statuses()
    {
        return $this->hasMany(TabelStatus::class, 'id_laporan');
    }

    // Status terbaru
    public function statusTerbaru()
    {
        return $this->hasOne(TabelStatus::class, 'id_laporan')
                    ->latestOfMany('created_at');
    }
}

