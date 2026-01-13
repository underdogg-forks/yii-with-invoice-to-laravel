<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitPeppol extends Model
{
    use HasFactory;

    protected $table = 'unit_peppol';

    protected $fillable = [
        'unit_id',
        'code',
        'name',
        'description',
    ];

    protected $casts = [
        'unit_id' => 'integer',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
