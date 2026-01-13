<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quotes';

    protected $fillable = [
        'quote_number',
        'client_id',
        'user_id',
        'status_id',
        'quote_date',
        'expiry_date',
        'subtotal',
        'tax_total',
        'discount_amount',
        'discount_percent',
        'total_amount',
        'notes',
        'terms_and_conditions',
        'url_key',
        'password',
        'is_read_only',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'sent_at',
        'viewed_at',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'expiry_date' => 'date',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_read_only' => 'boolean',
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(QuoteStatus::class, 'status_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function salesOrder(): HasOne
    {
        return $this->hasOne(SalesOrder::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status_id', QuoteStatus::STATUS_DRAFT);
    }

    public function scopeSent($query)
    {
        return $query->where('status_id', QuoteStatus::STATUS_SENT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status_id', QuoteStatus::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status_id', QuoteStatus::STATUS_REJECTED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status_id', QuoteStatus::STATUS_EXPIRED)
            ->orWhere(function ($q) {
                $q->where('expiry_date', '<', now())
                    ->whereNotIn('status_id', [QuoteStatus::STATUS_APPROVED, QuoteStatus::STATUS_REJECTED]);
            });
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status_id', [QuoteStatus::STATUS_DRAFT, QuoteStatus::STATUS_SENT, QuoteStatus::STATUS_VIEWED]);
    }

    // Helper methods
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast() 
            && !in_array($this->status_id, [QuoteStatus::STATUS_APPROVED, QuoteStatus::STATUS_REJECTED]);
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status_id, [QuoteStatus::STATUS_SENT, QuoteStatus::STATUS_VIEWED])
            && !$this->isExpired();
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status_id, [QuoteStatus::STATUS_SENT, QuoteStatus::STATUS_VIEWED])
            && $this->status_id != QuoteStatus::STATUS_APPROVED;
    }

    public function canBeConverted(): bool
    {
        return $this->status_id == QuoteStatus::STATUS_APPROVED 
            && !$this->salesOrder()->exists();
    }

    public function generateUrlKey(): string
    {
        return bin2hex(random_bytes(32));
    }
}
