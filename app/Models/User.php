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

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function isOnline(): bool
    {
        return $this->last_activity_at !== null
            && $this->last_activity_at->greaterThanOrEqualTo(now()->subMinutes(5));
    }

    public function dashboardRouteName(): ?string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
            default => null,
        };
    }

    public function monitoringRole(): ?string
    {
        return in_array($this->role, ['guru', 'siswa'], true) ? $this->role : null;
    }

    public function lastActiveLabel(): string
    {
        if ($this->isOnline()) {
            return 'Sekarang';
        }

        return $this->last_activity_at?->locale('id')->diffForHumans() ?? 'Belum pernah aktif';
    }

    public function scopeForRoles(Builder $query, string|array $roles): Builder
    {
        $roles = (array) $roles;

        return $query->whereIn('role', $roles);
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