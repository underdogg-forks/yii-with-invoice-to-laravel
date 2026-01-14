<?php

namespace App\Models;

use App\Enums\TemplateVariableTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateVariable extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $casts = [
        'is_required' => 'boolean',
        'type' => TemplateVariableTypeEnum::class,
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
     * Get applicabilities for this template variable
     */
    public function applicabilities(): HasMany
    {
        return $this->hasMany(TemplateVariableApplicability::class);
    }

    #endregion

    #region Accessors
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

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

    #endregion

    #region Custom Methods
    /*
    |--------------------------------------------------------------------------
    | Custom Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if variable is applicable to a template type
     */
    public function isApplicableTo(string $templateType): bool
    {
        return $this->applicabilities()->where('applicable_type', $templateType)->exists();
    }

    #endregion
}
