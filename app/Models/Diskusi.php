<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KomentarDiskusi;
use App\Models\DiskusiReaction;

class Diskusi extends Model
{
    use HasFactory;

    protected $table = 'diskusi';
    protected $fillable = ['user_id', 'judul', 'pesan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function komentars()
    {
        return $this->hasMany(KomentarDiskusi::class, 'diskusi_id');
    }

    public function reactions()
    {
        return $this->hasMany(DiskusiReaction::class, 'diskusi_id');
    }
}