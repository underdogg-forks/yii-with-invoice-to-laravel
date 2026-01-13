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

    protected $table = 'sales_orders';

    protected $fillable = [
        'so_number',
        'quote_id',
        'client_id',
        'user_id',
        'status_id',
        'order_date',
        'expected_delivery_date',
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
        'confirmed_by',
        'confirmed_at',
        'completed_by',
        'completed_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

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

    // Scopes
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

    // Helper methods
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
}
