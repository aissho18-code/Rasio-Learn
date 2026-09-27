<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submissions';

    protected $fillable = [
        'siswa_id',
        'tugas_id',
        'jawaban',
        'file_path',
        'nilai',
        'catatan_guru',
        'status',
        'ai_score',
        'ai_feedback',
        'final_score',
        'final_feedback',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }
}