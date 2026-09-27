<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'materi_id',
        'judul',
        'pekan',
        'questions',
        'file_path',
        'tenggat_waktu',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'questions' => 'array',
        'tenggat_waktu' => 'datetime',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'tugas_id');
    }
}