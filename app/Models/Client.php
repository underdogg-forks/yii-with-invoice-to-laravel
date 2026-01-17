<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\Communicable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes, Addressable, Communicable;

    public $timestamps = true;

    protected $table = 'clients';

    protected $casts = [
        'active' => 'boolean',
        'age' => 'integer',
        'gender' => 'integer',
        'birthdate' => 'date',
    ];

    protected $guarded = [];

    protected $appends = ['computed_full_name'];

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

    public function customFields(): HasMany
    {
        return $this->hasMany(ClientCustom::class, 'client_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function peppol(): HasOne
    {
        return $this->hasOne(ClientPeppol::class, 'client_id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductClient::class, 'client_id');
    }

    #endregion

    #region Accessors
    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getComputedFullNameAttribute(): string
    {
        if ($this->full_name) {
            return $this->full_name;
        }
        
        $parts = array_filter([$this->name, $this->surname]);
        return implode(' ', $parts);
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

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('surname', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('number', 'like', "%{$search}%");
        });
    }

    #endregion
}
