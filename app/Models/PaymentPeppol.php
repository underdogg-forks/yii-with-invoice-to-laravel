<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPeppol extends Model
{
    use HasFactory;

    protected $table = 'payment_peppol';

    protected $fillable = [
        'inv_id',
        'auto_reference',
        'provider',
    ];

    protected $casts = [
        'inv_id' => 'integer',
        'auto_reference' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'inv_id');
    }
}
