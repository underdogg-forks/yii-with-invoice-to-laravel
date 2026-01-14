<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'url',
        'method',
        'execution_time',
        'query_count',
        'query_time',
        'memory_usage',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'execution_time' => 'decimal:2',
        'query_time' => 'decimal:2',
        'query_count' => 'integer',
        'memory_usage' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
