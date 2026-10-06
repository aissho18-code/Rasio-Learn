<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'target_audience',
        'judul',
        'isi',
        'diterbitkan_at',
    ];

    protected $casts = [
        'diterbitkan_at' => 'datetime',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function reads()
    {
    return $this->hasMany(PengumumanRead::class, 'pengumuman_id');
    }
}