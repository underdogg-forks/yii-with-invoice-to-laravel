<?php

namespace Database\Factories;

use App\Models\RecoveryCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecoveryCodeFactory extends Factory
{
    protected $model = RecoveryCode::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'code' => hash('sha256', strtoupper(substr(bin2hex(random_bytes(5)), 0, 10))),
            'used_at' => null,
        ];
    }

    /**
     * Indicate that the code has been used
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_at' => now(),
        ]);
    }
}
