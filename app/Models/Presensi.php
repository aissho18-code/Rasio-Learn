<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'absensis'; // Jika tabel di database bernama absensis
    protected $guarded = ['id'];

    public function siswa()
    {
        // Menghubungkan kolom siswa_id di tabel absensis ke kolom id di tabel users
        return $this->belongsTo(User::class, 'siswa_id');
    }
}