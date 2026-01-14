<?php

namespace Database\Factories;

use App\Models\UnitPeppol;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitPeppolFactory extends Factory
{
    protected $model = UnitPeppol::class;

    public function definition(): array
    {
        return [
            'unit_id' => Unit::factory(),
            'code' => fake()->lexify('???'),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
