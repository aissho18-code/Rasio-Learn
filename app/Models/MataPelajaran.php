<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    protected $fillable = ['nama_mapel', 'guru_id'];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'mapel_id');
    }
}