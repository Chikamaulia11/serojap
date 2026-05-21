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

    // Opsional: Relasi agar Anda bisa tahu siapa admin yang posting FAQ
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}