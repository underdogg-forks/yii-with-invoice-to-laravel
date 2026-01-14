<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteStatus extends Model
{
    use HasFactory;

    protected $table = 'quote_statuses';

    protected $fillable = [
        'label',
        'sequence',
        'is_active',
    ];

    protected $casts = [
        'sequence' => 'integer',
        'is_active' => 'boolean',
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'status_id');
    }

    // Status constants
    public const STATUS_DRAFT = 1;
    public const STATUS_SENT = 2;
    public const STATUS_VIEWED = 3;
    public const STATUS_APPROVED = 4;
    public const STATUS_REJECTED = 5;
    public const STATUS_EXPIRED = 6;

    // Scope for active statuses
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
