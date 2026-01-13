<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'surname',
        'full_name',
        'email',
        'mobile',
        'phone',
        'fax',
        'title',
        'address_1',
        'address_2',
        'building_number',
        'city',
        'state',
        'zip',
        'country',
        'web',
        'vat_id',
        'tax_code',
        'language',
        'active',
        'number',
        'group',
        'frequency',
        'avs',
        'insured_number',
        'veka',
        'birthdate',
        'age',
        'gender',
        'postal_address_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'age' => 'integer',
        'gender' => 'integer',
        'birthdate' => 'date',
    ];

    protected $appends = ['computed_full_name'];

    public function getComputedFullNameAttribute(): string
    {
        if ($this->full_name) {
            return $this->full_name;
        }
        
        $parts = array_filter([$this->name, $this->surname]);
        return implode(' ', $parts);
    }

    // Relationships
    public function peppol(): HasOne
    {
        return $this->hasOne(ClientPeppol::class, 'client_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(ClientCustom::class, 'client_id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductClient::class, 'client_id');
    }

    // Scopes
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
}
