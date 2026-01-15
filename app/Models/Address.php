<?php

namespace App\Models;

use App\Enums\AddressTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $casts = [
        'type' => AddressTypeEnum::class,
        'is_default' => 'boolean',
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

    /**
     * Get the parent addressable model (Client, User, etc.)
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    #endregion

    #region Accessors
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Get full address as single string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);
        
        return implode(', ', $parts);
    }

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

    /**
     * Scope: Filter by type
     */
    public function scopeOfType($query, AddressTypeEnum $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    #endregion
}
