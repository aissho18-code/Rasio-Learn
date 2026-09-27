<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasBlock extends Model
{
    protected $table = 'aktivitas_blocks';

    protected $fillable = [
        'aktivitas_id', 'tahap', 'tipe', 'judul', 'urutan', 'konfigurasi'
    ];

    protected $casts = [
        'konfigurasi' => 'array',
    ];

    public function aktivitas()
    {
        return $this->belongsTo(Aktivitas::class, 'aktivitas_id');
    }
}