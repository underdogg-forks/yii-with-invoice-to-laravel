<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'client_id',
        'invoice_number',
        'date_issued',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'total_amount' => 'decimal:2',
        'date_issued' => 'date',
    ];

    public function peppolPayments(): HasMany
    {
        return $this->hasMany(PaymentPeppol::class, 'inv_id');
    }
}
