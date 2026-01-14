<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'abbreviation',
    ];

    public function peppol(): HasOne
    {
        return $this->hasOne(UnitPeppol::class, 'unit_id');
    }
}
