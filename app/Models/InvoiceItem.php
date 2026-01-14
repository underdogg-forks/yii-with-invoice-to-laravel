<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'tax_rate_id',
        'name',
        'description',
        'quantity',
        'price',
        'discount_amount',
        'discount_percent',
        'order',
        'product_unit',
        'product_sku',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'order' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    /**
     * Calculate subtotal for this item
     */
    public function getSubtotal(): float
    {
        $subtotal = $this->quantity * $this->price;
        
        if ($this->discount_amount > 0) {
            $subtotal -= $this->discount_amount;
        } elseif ($this->discount_percent > 0) {
            $subtotal -= $subtotal * ($this->discount_percent / 100);
        }
        
        return max(0, $subtotal);
    }

    /**
     * Calculate tax for this item
     */
    public function getTaxAmount(): float
    {
        if (!$this->taxRate) {
            return 0;
        }
        
        return $this->getSubtotal() * ($this->taxRate->rate / 100);
    }

    /**
     * Calculate total including tax
     */
    public function getTotal(): float
    {
        return $this->getSubtotal() + $this->getTaxAmount();
    }
}
