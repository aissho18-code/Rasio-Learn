<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumumanRead extends Model
{
    protected $table = 'pengumuman_reads';

    protected $fillable = [
        'pengumuman_id',
        'siswa_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}