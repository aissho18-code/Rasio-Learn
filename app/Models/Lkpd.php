<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lkpd extends Model
{
    protected $table = 'lkpds';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'judul',
        'deskripsi',
        'instruksi',
        'deadline',
        'modul_path',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function questions()
    {
        return $this->hasMany(LkpdQuestion::class, 'lkpd_id')->orderBy('urutan');
    }

    public function submissions()
    {
        return $this->hasMany(LkpdSubmission::class, 'lkpd_id');
    }
}