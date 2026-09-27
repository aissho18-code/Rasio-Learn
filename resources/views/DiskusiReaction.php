<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskusiReaction extends Model
{
    use HasFactory;

    protected $table = 'diskusi_reactions';
    protected $fillable = ['diskusi_id', 'user_id', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}