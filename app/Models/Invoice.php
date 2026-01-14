<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'invoices';

    protected $casts = [
        'date_created' => 'date',
        'date_modified' => 'date',
        'date_supplied' => 'date',
        'date_due' => 'date',
        'date_tax_point' => 'date',
        'date_paid_off' => 'date',
        'date_issued' => 'date',
        'discount_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_read_only' => 'boolean',
        'client_id' => 'integer',
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

    public function numbering(): BelongsTo
    {
        return $this->belongsTo(InvoiceNumbering::class, 'numbering_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InvoiceStatus::class, 'status_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('order');
    }

    public function amount(): HasOne
    {
        return $this->hasOne(InvoiceAmount::class);
    }

    public function peppolPayments(): HasMany
    {
        return $this->hasMany(PaymentPeppol::class, 'inv_id');
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
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->date_due) {
            return false;
        }

        return $this->date_due->isPast() && !$this->isPaid();
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' || ($this->status_id && $this->status_id === 4);
    }

    /**
     * Generate unique URL key for guest access
     */
    public function generateUrlKey(): string
    {
        return bin2hex(random_bytes(16));
    }

    #endregion
}
