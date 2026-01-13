<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
    ];

    public function peppol(): HasOne
    {
        return $this->hasOne(ClientPeppol::class, 'client_id');
    }
}
