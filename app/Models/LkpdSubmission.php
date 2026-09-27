<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkpdSubmission extends Model
{
    protected $table = 'lkpd_submissions';

    protected $fillable = [
        'lkpd_id',
        'siswa_id',
        'jawaban',
        'nilai',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'jawaban' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function lkpd()
    {
        return $this->belongsTo(Lkpd::class, 'lkpd_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}