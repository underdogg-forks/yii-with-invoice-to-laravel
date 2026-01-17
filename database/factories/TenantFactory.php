<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'subdomain' => fake()->unique()->slug(2),
            'domain' => null,
            'database' => null,
            'is_active' => true,
            'trial_ends_at' => null,
            'subscribed_at' => now(),
        ];
    }

    /**
     * Indicate that the tenant is on trial
     */
    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'trial_ends_at' => now()->addDays(14),
            'subscribed_at' => null,
        ]);
    }

    /**
     * Indicate that the tenant is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
