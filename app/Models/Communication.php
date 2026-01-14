<?php

namespace App\Models;

use App\Enums\CommunicationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Communication extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $casts = [
        'type' => CommunicationTypeEnum::class,
        'is_primary' => 'boolean',
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
     * Get the parent communicable model (Client, User, etc.)
     */
    public function communicable(): MorphTo
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
     * Get formatted value based on type
     */
    public function getFormattedValueAttribute(): string
    {
        return match($this->type) {
            CommunicationTypeEnum::PHONE, 
            CommunicationTypeEnum::MOBILE, 
            CommunicationTypeEnum::FAX => $this->formatPhone($this->value),
            default => $this->value,
        };
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
    public function scopeOfType($query, CommunicationTypeEnum $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Primary communications
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Format phone number
     */
    private function formatPhone(string $phone): string
    {
        // Remove non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        // Format based on length (simple formatting)
        if (strlen($cleaned) == 10) {
            return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $cleaned);
        }
        
        return $phone;
    }

    #endregion
}
