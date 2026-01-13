<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordResetToken;
use App\DTOs\LoginDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Attempt to log in a user
     */
    public function login(LoginDTO $dto): bool
    {
        $credentials = [
            'email' => $dto->email,
            'password' => $dto->password,
        ];

        if (Auth::attempt($credentials, $dto->remember)) {
            $user = Auth::user();

            // If 2FA is enabled, log out and require 2FA verification
            if ($user->hasTwoFactorEnabled()) {
                Auth::logout();
                return false; // Indicates 2FA required
            }

            return true;
        }

        return false;
    }

    /**
     * Verify 2FA and log in user
     */
    public function loginWith2FA(string $email, string $password, string $code, bool $remember = false): bool
    {
        $user = $this->userService->getByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        // Try 2FA code first
        if ($this->userService->verify2FACode($user, $code)) {
            Auth::login($user, $remember);
            return true;
        }

        // Try recovery code
        if ($user->verifyRecoveryCode($code)) {
            Auth::login($user, $remember);
            return true;
        }

        return false;
    }

    /**
     * Log out the current user
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Create a password reset token
     */
    public function createPasswordResetToken(string $email): ?string
    {
        $user = $this->userService->getByEmail($email);

        if (!$user) {
            return null;
        }

        $token = Str::random(64);

        PasswordResetToken::updateOrCreate(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        return $token;
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        $resetToken = PasswordResetToken::where('email', $email)->first();

        if (!$resetToken || $resetToken->isExpired()) {
            return false;
        }

        if (!Hash::check($token, $resetToken->token)) {
            return false;
        }

        $user = $this->userService->getByEmail($email);

        if (!$user) {
            return false;
        }

        $user->update(['password' => Hash::make($newPassword)]);
        $resetToken->delete();

        return true;
    }
}
