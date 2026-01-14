<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'email';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Check if the token has expired (1 hour)
     */
    public function isExpired(): bool
    {
        if ($this->created_at === null) {
            return true; // Treat missing timestamp as expired
        }
        return $this->created_at->addHour()->isPast();
    }
}
