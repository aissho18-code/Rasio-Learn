<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreTest extends Model
{
    protected $table = 'pre_tests';
    protected $fillable = ['siswa_id', 'detail'];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function hasil()
    {
        return $this->hasMany(PreTestHasil::class, 'pre_test_id');
    }
}