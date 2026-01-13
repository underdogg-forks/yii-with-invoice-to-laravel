<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'invoice_number' => fake()->unique()->numerify('INV-####'),
            'date_issued' => fake()->date(),
            'total_amount' => fake()->randomFloat(2, 100, 10000),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'cancelled']),
        ];
    }
}
