<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'login' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'), // Default password
            'tfa_enabled' => false,
            'totp_secret' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user has 2FA enabled
     */
    public function twoFactorEnabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'tfa_enabled' => true,
            'totp_secret' => 'JBSWY3DPEHPK3PXP', // Test secret
        ]);
    }

    /**
     * Indicate that the user's email is unverified
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
