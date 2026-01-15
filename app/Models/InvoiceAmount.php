<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceAmount extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $casts = [
        'item_subtotal' => 'decimal:2',
        'item_tax_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'balance' => 'decimal:2',
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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
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

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Recalculate all amounts from invoice items
     */
    public function recalculate(): void
    {
        $invoice = $this->invoice()->with('items.taxRate')->first();
        
        $itemSubtotal = 0;
        $itemTaxTotal = 0;
        
        foreach ($invoice->items as $item) {
            $itemSubtotal += $item->getSubtotal();
            $itemTaxTotal += $item->getTaxAmount();
        }
        
        // Apply invoice-level discount
        $discount = $invoice->discount_amount;
        if ($invoice->discount_percent > 0) {
            $discount = $itemSubtotal * ($invoice->discount_percent / 100);
        }
        
        $total = $itemSubtotal + $itemTaxTotal - $discount;
        $balance = $total - $this->paid;
        
        $this->update([
            'item_subtotal' => $itemSubtotal,
            'item_tax_total' => $itemTaxTotal,
            'tax_total' => $itemTaxTotal,
            'discount' => $discount,
            'total' => $total,
            'balance' => $balance,
        ]);
    }

    #endregion
}
