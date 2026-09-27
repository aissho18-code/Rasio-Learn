<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
        'role',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class, 'user_id');
    }

    public function guruProfile()
    {
        return $this->hasOne(GuruProfile::class, 'user_id');
    }
}