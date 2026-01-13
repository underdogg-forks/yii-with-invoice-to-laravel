<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderStatus extends Model
{
    use HasFactory;

    protected $table = 'sales_order_statuses';

    protected $fillable = [
        'label',
        'sequence',
        'is_active',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'is_active' => 'boolean',
    ];

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'status_id');
    }

    // Status constants
    public const STATUS_PENDING = 1;
    public const STATUS_CONFIRMED = 2;
    public const STATUS_PROCESSING = 3;
    public const STATUS_COMPLETED = 4;
    public const STATUS_CANCELLED = 5;

    // Scope for active statuses
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
