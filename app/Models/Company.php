<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'short_name', 'nip', 'regon', 'krs', 'email', 'phone',
        'address', 'postal_code', 'city', 'notes', 'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'can_write'])
            ->withTimestamps();
    }

    public function registers(): HasMany
    {
        return $this->hasMany(Register::class);
    }

    public function authorizations(): HasMany
    {
        return $this->hasMany(Authorization::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }
}
