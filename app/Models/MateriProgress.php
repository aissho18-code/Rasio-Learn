<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriProgress extends Model
{
    protected $table = 'materi_progress';
    protected $fillable = ['siswa_id', 'materi_id', 'status', 'unlocked_by', 'unlocked_at'];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}