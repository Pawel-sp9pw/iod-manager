<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'is_super_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->withPivot(['role', 'can_write'])
            ->withTimestamps();
    }

    public function canAccessCompany(int $companyId): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->companies()->whereKey($companyId)->exists();
    }

    public function canWriteCompany(int $companyId): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return $this->companies()
            ->whereKey($companyId)
            ->where(function ($query) {
                $query->where('company_user.role', 'iod')
                    ->orWhere('company_user.can_write', true);
            })
            ->exists();
    }
}
