<?php

namespace Database\Factories;

use App\Models\SalesOrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesOrderStatusFactory extends Factory
{
    protected $model = SalesOrderStatus::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'name' => $this->faker->words(2, true),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'pending',
            'name' => 'Pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'confirmed',
            'name' => 'Confirmed',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'completed',
            'name' => 'Completed',
        ]);
    }
}
