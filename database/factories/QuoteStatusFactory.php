<?php

namespace Database\Factories;

use App\Models\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteStatusFactory extends Factory
{
    protected $model = QuoteStatus::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word(),
            'name' => $this->faker->words(2, true),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'draft',
            'name' => 'Draft',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'sent',
            'name' => 'Sent',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'approved',
            'name' => 'Approved',
        ]);
    }
}
