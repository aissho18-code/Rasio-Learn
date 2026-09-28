<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    protected $table = 'aktivitas';

    protected $fillable = [
        'guru_id', 'kelas_id', 'judul', 'topik', 'tujuan', 'petunjuk', 'tipe_penyerahan', 'status', 'lkpd_path',
        'published_at', 'respons_type', 'pertanyaan'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function blocks()
    {
        return $this->hasMany(AktivitasBlock::class, 'aktivitas_id')->orderBy('urutan');
    }

    public function submissions()
    {
        return $this->hasMany(AktivitasSubmission::class, 'aktivitas_id');
    }
}