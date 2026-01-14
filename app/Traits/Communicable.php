<?php

namespace App\Traits;

use App\Models\Communication;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Communicable
{
    #region Relationships
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get all communications for this entity.
     */
    public function communications(): MorphMany
    {
        return $this->morphMany(Communication::class, 'communicable');
    }

    #endregion

    #region Helper Methods
    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the primary communication for this entity.
     */
    public function getPrimaryCommunication(): ?Communication
    {
        return $this->communications()->where('is_primary', true)->first();
    }

    /**
     * Get communications by type.
     */
    public function getCommunicationsByType(string $type): \Illuminate\Database\Eloquent\Collection
    {
        return $this->communications()->where('type', $type)->get();
    }

    /**
     * Set a communication as primary (and unset others).
     */
    public function setPrimaryCommunication(int $communicationId): bool
    {
        // Early return if communication doesn't belong to this entity
        $communication = $this->communications()->find($communicationId);
        if (!$communication) {
            return false;
        }

        // Unset all other primary communications
        $this->communications()->update(['is_primary' => false]);

        // Set this one as primary
        $communication->update(['is_primary' => true]);

        return true;
    }

    /**
     * Get primary phone number.
     */
    public function getPrimaryPhone(): ?string
    {
        $phone = $this->communications()
            ->where('type', 'phone')
            ->where('is_primary', true)
            ->first();

        return $phone?->value;
    }

    /**
     * Get primary email address.
     */
    public function getPrimaryEmail(): ?string
    {
        $email = $this->communications()
            ->where('type', 'email')
            ->where('is_primary', true)
            ->first();

        return $email?->value;
    }

    #endregion
}
