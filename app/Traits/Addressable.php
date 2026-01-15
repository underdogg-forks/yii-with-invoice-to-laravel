<?php

namespace App\Traits;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Addressable
{
    #region Relationships
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all addresses for this entity.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    #endregion

    #region Helper Methods
    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the primary address for this entity.
     */
    public function getPrimaryAddress(): ?Address
    {
        return $this->addresses()->where('is_primary', true)->first();
    }

    /**
     * Get addresses by type.
     */
    public function getAddressesByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return $this->addresses()->where('type', $type)->get();
    }

    /**
     * Set an address as primary (and unset others).
     */
    public function setPrimaryAddress(int $addressId): bool
    {
        // Early return if address doesn't belong to this entity
        $address = $this->addresses()->find($addressId);
        if (!$address) {
            return false;
        }

        // Unset all other primary addresses
        $this->addresses()->update(['is_primary' => false]);

        // Set this one as primary
        $address->update(['is_primary' => true]);

        return true;
    }

    #endregion
}
