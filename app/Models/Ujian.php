<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujians';
    protected $fillable = ['judul_ujian', 'deskripsi_ujian', 'questions'];

    protected $casts = [
        'questions' => 'array',
    ];
}