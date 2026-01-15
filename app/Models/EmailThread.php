<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailThread extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $casts = [
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'is_archived' => 'boolean',
        'last_message_at' => 'datetime',
        'message_count' => 'integer',
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
     * Get the user who owns this thread
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all messages in this thread
     */
    public function messages(): HasMany
    {
        return $this->hasMany(EmailMessage::class, 'thread_id');
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
     * Scope: Unread threads
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Starred threads
     */
    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    /**
     * Scope: Archived threads
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope: Active (not archived) threads
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the latest message
     */
    public function latestMessage()
    {
        return $this->messages()->latest('sent_at')->first();
    }

    /**
     * Mark thread as read
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Mark thread as unread
     */
    public function markAsUnread(): void
    {
        $this->update(['is_read' => false]);
    }

    /**
     * Toggle starred status
     */
    public function toggleStar(): void
    {
        $this->update(['is_starred' => !$this->is_starred]);
    }

    /**
     * Archive thread
     */
    public function archive(): void
    {
        $this->update(['is_archived' => true]);
    }

    /**
     * Unarchive thread
     */
    public function unarchive(): void
    {
        $this->update(['is_archived' => false]);
    }

    #endregion
}
