<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderCompletion extends Model
{
    protected $fillable = ['reminder_id', 'completed_by', 'completed_at', 'notes'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function reminder(): BelongsTo
    {
        return $this->belongsTo(Reminder::class);
    }
}
