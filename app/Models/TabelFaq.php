<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelFaq extends Model
{
    protected $table = 'tabel_faq';
    protected $primaryKey = 'id_faq';

    // WAJIB: user_id harus masuk ke sini!
    protected $fillable = [
        'user_id',
        'pertanyaan',
        'jawaban',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    // Relasi ke admin yang menulis FAQ ini.
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias supaya view tidak perlu tahu nama aslinya.
    public function admin()
    {
        return $this->user()->withTrashed();
    }
}
