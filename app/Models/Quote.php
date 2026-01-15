<?php

namespace App\Models;

use App\Enums\QuoteStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $table = 'quotes';

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
        'status' => QuoteStatusEnum::class,
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function scopeDraft($query)
    {
        return $query->where('status', QuoteStatusEnum::Draft);
    }

    public function scopeSent($query)
    {
        return $query->where('status', QuoteStatusEnum::Sent);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', QuoteStatusEnum::Approved);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', QuoteStatusEnum::Rejected);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', QuoteStatusEnum::Expired)
            ->orWhere(function ($q) {
                $q->where('expiry_date', '<', now())
                    ->whereNotIn('status', [QuoteStatusEnum::Approved, QuoteStatusEnum::Rejected]);
            });
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [QuoteStatusEnum::Draft, QuoteStatusEnum::Sent, QuoteStatusEnum::Viewed]);
    }

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast() 
            && !in_array($this->status, [QuoteStatusEnum::Approved, QuoteStatusEnum::Rejected]);
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, [QuoteStatusEnum::Sent, QuoteStatusEnum::Viewed])
            && !$this->isExpired();
    }

    public function canBeRejected(): bool
    {
        return in_array($this->status, [QuoteStatusEnum::Sent, QuoteStatusEnum::Viewed])
            && $this->status != QuoteStatusEnum::Approved;
    }

    public function canBeConverted(): bool
    {
        return $this->status == QuoteStatusEnum::Approved 
            && !$this->salesOrder()->exists();
    }

    public function generateUrlKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    #endregion
}
