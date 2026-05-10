<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelFaq extends Model
{
    protected $table = 'Tabel_FAQ';
    protected $primaryKey = 'id_faq';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'pertanyaan',
        'jawaban',
        'urutan',
    ];
}
