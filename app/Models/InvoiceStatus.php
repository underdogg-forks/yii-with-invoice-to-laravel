<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceStatus extends Model
{
    use HasFactory;

    // Status constants
    public const DRAFT = 1;
    public const SENT = 2;
    public const VIEWED = 3;
    public const PAID = 4;
    public const CANCELLED = 5;
    public const OVERDUE = 6;

    protected $fillable = [
        'name',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'status_id');
    }
}
