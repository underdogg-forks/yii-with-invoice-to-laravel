<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailMessage extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
        'is_html' => 'boolean',
        'is_read' => 'boolean',
        'is_draft' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    protected $guarded = [];

    #region Static Methods
    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    #endregion

    #region Relationships
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the thread this message belongs to
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(EmailThread::class, 'thread_id');
    }

    /**
     * Get the user who owns this message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all attachments for this message
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(EmailAttachment::class, 'email_message_id');
    }

    /**
     * Get the related entity (invoice, quote, etc.)
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related');
    }

    #endregion

    #region Accessors
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    #endregion

    #region Mutators
    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    #endregion

    #region Scopes
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: Sent messages
     */
    public function scopeSent($query)
    {
        return $query->where('direction', 'sent');
    }

    /**
     * Scope: Received messages
     */
    public function scopeReceived($query)
    {
        return $query->where('direction', 'received');
    }

    /**
     * Scope: Read messages
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope: Unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Draft messages
     */
    public function scopeDrafts($query)
    {
        return $query->where('is_draft', true);
    }

    /**
     * Scope: Filter by thread
     */
    public function scopeByThread($query, int $threadId)
    {
        return $query->where('thread_id', $threadId);
    }

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark message as opened
     */
    public function markAsOpened(): void
    {
        $this->update(['opened_at' => now()]);
    }

    /**
     * Check if message is sent
     */
    public function isSent(): bool
    {
        return $this->direction === 'sent';
    }

    /**
     * Check if message is received
     */
    public function isReceived(): bool
    {
        return $this->direction === 'received';
    }

    #endregion
}
