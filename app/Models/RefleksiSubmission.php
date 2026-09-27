<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefleksiSubmission extends Model
{
    protected $table = 'refleksi_submissions';

    protected $fillable = [
        'refleksi_id',
        'siswa_id',
        'answers',
        'catatan',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function refleksi()
    {
        return $this->belongsTo(Refleksi::class, 'refleksi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}