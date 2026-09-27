<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruProfile extends Model
{
    protected $table = 'guru_profiles';
    protected $fillable = ['user_id', 'nip', 'mapel_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
}