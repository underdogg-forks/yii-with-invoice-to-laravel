<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'login',
        'email',
        'password',
        'tfa_enabled',
        'totp_secret',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'totp_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tfa_enabled' => 'boolean',
    ];

    public function recoveryCodes(): HasMany
    {
        return $this->hasMany(RecoveryCode::class);
    }

    public function passwordResetTokens(): HasMany
    {
        return $this->hasMany(PasswordResetToken::class, 'email', 'email');
    }

    /**
     * Generate new recovery codes for 2FA
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        // Delete old codes
        $this->recoveryCodes()->delete();

        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));
            $codes[] = $code;
            
            $this->recoveryCodes()->create([
                'code' => hash('sha256', $code),
            ]);
        }

        return $codes;
    }

    /**
     * Verify a recovery code
     */
    public function verifyRecoveryCode(string $code): bool
    {
        $hashedCode = hash('sha256', $code);
        
        $recoveryCode = $this->recoveryCodes()
            ->where('code', $hashedCode)
            ->whereNull('used_at')
            ->first();

        if ($recoveryCode) {
            $recoveryCode->update(['used_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Check if user has 2FA enabled
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->tfa_enabled && !empty($this->totp_secret);
    }
}
