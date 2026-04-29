<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'nama_pelapor',
        'foto',
        'alamat',
        'latitude',
        'longitude',
        'keterangan',
        'status'
    ];

    // RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}