<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelLaporan extends Model
{
    protected $table = 'Tabel_Laporan';
    protected $primaryKey = 'id_laporan';
    public $timestamps = false;

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
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Tabel_Status (bisa banyak riwayat status)
    public function statuses()
    {
        return $this->hasMany(TabelStatus::class, 'id_laporan', 'id_laporan');
    }

    // Ambil status terbaru saja
    public function statusTerbaru()
    {
        return $this->hasOne(TabelStatus::class, 'id_laporan', 'id_laporan')
                    ->latestOfMany('created_at');
    }
}
