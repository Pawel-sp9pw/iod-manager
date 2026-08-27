<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegisterEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'register_id', 'title', 'status', 'event_date',
        'data', 'notes', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'data' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(Register::class);
    }
}
