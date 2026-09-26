<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelStatus extends Model
{
    use HasFactory;

    /* =========================
       TABLE
    ========================= */
    protected $table = 'tabel_status';

    /* =========================
       PRIMARY KEY
    ========================= */
    protected $primaryKey = 'id_status';

    /* =========================
       AUTO INCREMENT
    ========================= */
    public $incrementing = true;

    /* =========================
       KEY TYPE
    ========================= */
    protected $keyType = 'int';

    /* =========================
       FILLABLE
    ========================= */
    protected $fillable = [

        'report_id',
        'user_id',
        'status',
        'keterangan',
        'foto_perbaikan',

    ];

    /* =========================
       CAST
    ========================= */
    protected $casts = [

        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    /* =========================
       RELASI KE REPORT
    ========================= */
    public function laporan()
    {
        return $this->belongsTo(
            Report::class,
            'report_id'
        );
    }

    /* =========================
       RELASI KE ADMIN
    ========================= */
    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /* =========================
       ALIAS USER
    ========================= */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}