<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelStatus extends Model
{
    // Pastikan nama tabel sesuai database: `tabel_status`
    protected $table = 'tabel_status';
    protected $primaryKey = 'id_status';
    public $timestamps = true;


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
