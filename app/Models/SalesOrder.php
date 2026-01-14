<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $table = 'sales_orders';

    protected $casts = [
        'quote_date' => 'date',
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_read_only' => 'boolean',
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

    public function status(): BelongsTo
    {
        return $this->belongsTo(SalesOrderStatus::class, 'status_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'so_id');
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

    public function scopePending($query)
    {
        return $query->where('status_id', SalesOrderStatus::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status_id', SalesOrderStatus::STATUS_CONFIRMED);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status_id', QuoteStatus::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status_id', SalesOrderStatus::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status_id', SalesOrderStatus::STATUS_CANCELLED);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status_id', [
            SalesOrderStatus::STATUS_PENDING,
            SalesOrderStatus::STATUS_CONFIRMED,
            SalesOrderStatus::STATUS_PROCESSING,
        ]);
    }

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    public function canBeConfirmed(): bool
    {
        return $this->status_id == SalesOrderStatus::STATUS_PENDING;
    }

    public function canBeProcessed(): bool
    {
        return $this->status_id == SalesOrderStatus::STATUS_CONFIRMED;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status_id, [SalesOrderStatus::STATUS_CONFIRMED, SalesOrderStatus::STATUS_PROCESSING]);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status_id, [SalesOrderStatus::STATUS_COMPLETED, SalesOrderStatus::STATUS_CANCELLED]);
    }

    public function canBeConvertedToInvoice(): bool
    {
        return $this->status_id == SalesOrderStatus::STATUS_COMPLETED 
            && !$this->invoice()->exists();
    }

    public function generateUrlKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    #endregion
}
