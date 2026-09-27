<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomentarDiskusi extends Model
{
    use HasFactory;

    protected $table = 'diskusi_komentar';
    protected $fillable = ['diskusi_id', 'user_id', 'pesan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}