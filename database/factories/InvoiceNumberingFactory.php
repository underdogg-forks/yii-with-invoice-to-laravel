<?php

namespace Database\Factories;

use App\Models\InvoiceNumbering;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceNumberingFactory extends Factory
{
    protected $model = InvoiceNumbering::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'prefix' => strtoupper($this->faker->lexify('???')),
            'next_number' => $this->faker->numberBetween(1, 1000),
            'left_pad' => 4,
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'name' => 'Default Invoice Numbering',
            'prefix' => 'INV',
        ]);
    }
}
