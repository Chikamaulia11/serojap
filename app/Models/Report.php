<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /* =========================
       TABLE
    ========================= */
    protected $table = 'reports';

    /* =========================
       FILLABLE
    ========================= */
    protected $fillable = [

        'user_id',
        'nama_pelapor',
        'foto',
        'alamat',
        'latitude',
        'longitude',
        'keterangan',

    ];

    /* =========================
       CAST
    ========================= */
    protected $casts = [

        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    /* =========================
       RELASI KE USER / PELAPOR
    ========================= */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /* =========================
       RELASI KE HISTORY STATUS
    ========================= */
    public function statuses()
    {
        return $this->hasMany(
            TabelStatus::class,
            'report_id'
        )->latest();
    }

    /* =========================
       STATUS TERBARU
    ========================= */
    public function latestStatus()
    {
        return $this->hasOne(
            TabelStatus::class,
            'report_id'
        )->latestOfMany();
    }

    /* =========================
       ALIAS STATUS TERBARU
       (BACKWARD COMPATIBLE)
    ========================= */
    public function statusTerbaru()
    {
        return $this->latestStatus();
    }
}