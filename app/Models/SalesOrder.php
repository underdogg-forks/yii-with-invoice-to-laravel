<?php

namespace App\Models;

use App\Enums\SalesOrderStatusEnum;
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
        'status' => SalesOrderStatusEnum::class,
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

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'so_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the user who created this sales order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
        return $query->where('status', SalesOrderStatusEnum::Draft);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', SalesOrderStatusEnum::Confirmed);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', SalesOrderStatusEnum::Processing);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', SalesOrderStatusEnum::Delivered);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', SalesOrderStatusEnum::Cancelled);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            SalesOrderStatusEnum::Draft,
            SalesOrderStatusEnum::Confirmed,
            SalesOrderStatusEnum::Processing,
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
        return $this->status == SalesOrderStatusEnum::Draft;
    }

    public function canBeProcessed(): bool
    {
        return $this->status == SalesOrderStatusEnum::Confirmed;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, [SalesOrderStatusEnum::Confirmed, SalesOrderStatusEnum::Processing]);
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, [SalesOrderStatusEnum::Delivered, SalesOrderStatusEnum::Cancelled]);
    }

    public function canBeConvertedToInvoice(): bool
    {
        return $this->status == SalesOrderStatusEnum::Delivered 
            && !$this->invoice()->exists();
    }

    public function generateUrlKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    #endregion
}
