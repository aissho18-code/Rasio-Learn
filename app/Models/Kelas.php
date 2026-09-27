<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'wali_kelas_id', 'jadwal', 'kapasitas'];

    public function wali() 
    { 
        return $this->belongsTo(\App\Models\User::class, 'wali_kelas_id'); 
    }

    public function siswa()
    {
        return $this->hasManyThrough(
            \App\Models\User::class,
            \App\Models\SiswaProfile::class,
            'kelas_id', // FK di siswa_profiles
            'id',       // FK di users
            'id',       // Local key kelas
            'user_id'   // Local key siswa_profiles
        );
    }

    public function guru()
    {
        return $this->belongsTo(\App\Models\User::class, 'wali_kelas_id');
    }

    public function siswaCount()
    {
        return $this->siswa()->count();
    }
}