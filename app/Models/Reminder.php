<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reminder extends Model
{
    protected $fillable = [
        'company_id', 'assigned_to', 'title', 'description', 'due_at',
        'recurrence', 'custom_interval_days', 'next_due_at',
        'email_notification', 'active',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'next_due_at' => 'datetime',
            'email_notification' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(ReminderCompletion::class);
    }

    public function calculateNextDue(CarbonInterface $from): ?CarbonInterface
    {
        return match ($this->recurrence) {
            'daily' => $from->copy()->addDay(),
            'weekly' => $from->copy()->addWeek(),
            'monthly' => $from->copy()->addMonthNoOverflow(),
            'quarterly' => $from->copy()->addMonthsNoOverflow(3),
            'yearly' => $from->copy()->addYearNoOverflow(),
            'custom' => $from->copy()->addDays(max(1, (int) $this->custom_interval_days)),
            default => null,
        };
    }
}
