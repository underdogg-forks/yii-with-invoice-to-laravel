<?php

namespace Database\Factories;

use App\Models\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceStatusFactory extends Factory
{
    protected $model = InvoiceStatus::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Draft', 'Sent', 'Viewed', 'Approved', 'Rejected', 'Paid']),
            'label' => $this->faker->word(),
        ];
    }
}
