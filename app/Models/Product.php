<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'unit_id',
        'tax_rate_id',
        'product_sku',
        'product_name',
        'product_description',
        'product_price',
        'purchase_price',
        'is_sold_as_service',
        'product_tariff',
        'sort_order',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'is_sold_as_service' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(ProductFamily::class, 'family_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
