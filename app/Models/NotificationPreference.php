<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'channel', // email, database, push
        'type', // invoice_created, quote_approved, etc.
        'is_enabled',
        'frequency', // immediate, daily, weekly
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the user who owns these preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notifications are enabled for this channel/type
     */
    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    /**
     * Check if immediate notifications are enabled
     */
    public function isImmediate(): bool
    {
        return $this->frequency === 'immediate';
    }
}
