<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabelFaq extends Model
{
    protected $table = 'tabel_faq'; 

    protected $primaryKey = 'id_faq'; 

    protected $fillable = [
        'user_id',
        'pertanyaan',
        'jawaban',
        'urutan' 
    ];
}
