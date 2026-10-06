<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkpdQuestion extends Model
{
    protected $table = 'lkpd_questions';

    protected $fillable = [
        'lkpd_id',
        'urutan',
        'pertanyaan',
        'gambar_path',
        'rubrik_jawaban',
        'pembahasan',
    ];

    public function lkpd()
    {
        return $this->belongsTo(Lkpd::class, 'lkpd_id');
    }
}