<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refleksi extends Model
{
    protected $table = 'refleksis';

    protected $fillable = [
        'guru_id',
        'judul_refleksi',
        'pertemuan',
        'deskripsi',
        'questions',
        'status',
    ];

    protected $casts = [
        'questions' => 'array',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function submissions()
    {
        return $this->hasMany(RefleksiSubmission::class, 'refleksi_id');
    }
}