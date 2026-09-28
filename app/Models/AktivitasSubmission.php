<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasSubmission extends Model
{
    protected $table = 'aktivitas_submissions';

    protected $fillable = [
        'aktivitas_id', 'siswa_id', 'jawaban', 'status', 'submitted_at', 'text_answer', 'file_path'
    ];

    protected $casts = [
        'jawaban' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function aktivitas()
    {
        return $this->belongsTo(Aktivitas::class, 'aktivitas_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}