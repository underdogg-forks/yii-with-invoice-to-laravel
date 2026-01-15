<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'custom_fields';

    protected $casts = [
        'required' => 'boolean',
        'order' => 'integer',
    ];

    protected $guarded = [];

    // Available field types
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SELECT = 'select';
    const TYPE_DATE = 'date';
    const TYPE_NUMBER = 'number';

    // Available locations
    const LOCATION_CLIENT = 'client';
    const LOCATION_INVOICE = 'invoice';
    const LOCATION_QUOTE = 'quote';
    const LOCATION_PRODUCT = 'product';

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

    public function clientCustoms(): HasMany
    {
        return $this->hasMany(ClientCustom::class, 'custom_field_id');
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

    public function scopeForClients($query)
    {
        return $query->where('table_name', self::LOCATION_CLIENT);
    }

    public function scopeActive($query)
    {
        return $query->orderBy('order', 'asc');
    }

    #endregion
}
