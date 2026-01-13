<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductFamily extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_name',
        'family_description',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'family_id');
    }
}
