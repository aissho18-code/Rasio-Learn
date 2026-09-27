<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeForRoles(Builder $query, string|array $roles): Builder
    {
        $roles = (array) $roles;

        return $query->where(function (Builder $query) use ($roles) {
            $query->whereIn('role', $roles)
                ->orWhereHas('roles', fn (Builder $roleQuery) => $roleQuery->whereIn('name', $roles));
        });
    }

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class, 'user_id');
    }

    public function guruProfile()
    {
        return $this->hasOne(GuruProfile::class, 'user_id');
    }
}