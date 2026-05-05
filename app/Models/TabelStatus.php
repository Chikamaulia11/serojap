<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelStatus extends Model
{
    protected $table = 'Tabel_Status';
    protected $primaryKey = 'id_status';
    public $timestamps = false;

    protected $fillable = [
        'id_laporan',
        'user_id',
        'status',
        'keterangan',
        'foto_perbaikan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relasi ke Tabel_Laporan
    public function laporan()
    {
        return $this->belongsTo(TabelLaporan::class, 'id_laporan', 'id_laporan');
    }

    // Relasi ke User (admin yang update)
    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
