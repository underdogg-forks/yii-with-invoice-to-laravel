<?php

namespace Database\Factories;

use App\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFieldFactory extends Factory
{
    protected $model = CustomField::class;

    public function definition(): array
    {
        return [
            'custom_field_table' => 'ip_clients',
            'custom_field_label' => $this->faker->words(2, true),
            'custom_field_type' => $this->faker->randomElement(['text', 'textarea', 'checkbox', 'select', 'date', 'number']),
            'custom_field_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_field_type' => 'text',
        ]);
    }

    public function textarea(): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_field_type' => 'textarea',
        ]);
    }

    public function checkbox(): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_field_type' => 'checkbox',
        ]);
    }

    public function select(): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_field_type' => 'select',
        ]);
    }

    public function date(): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_field_type' => 'date',
        ]);
    }

    public function number(): static
    {
        return $this->state(fn (array $attributes) => [
            'custom_field_type' => 'number',
        ]);
    }
}
