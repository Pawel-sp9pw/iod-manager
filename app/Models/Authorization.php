<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Authorization extends Model
{
    protected $fillable = [
        'company_id', 'authorization_number', 'person_name', 'person_identifier',
        'position', 'scope', 'issued_at', 'valid_until', 'revoked_at',
        'revocation_reason', 'issued_by', 'revoked_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'valid_until' => 'date',
            'revoked_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->revoked_at === null
            && ($this->valid_until === null || $this->valid_until->isFuture() || $this->valid_until->isToday());
    }
}
