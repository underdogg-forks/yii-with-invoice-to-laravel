<?php

namespace Database\Factories;

use App\Models\PaymentPeppol;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentPeppolFactory extends Factory
{
    protected $model = PaymentPeppol::class;

    public function definition(): array
    {
        return [
            'inv_id' => Invoice::factory(),
            'auto_reference' => time(),
            'provider' => fake()->randomElement(['StoreCove', 'Ecosio', 'Peppol', 'Other']),
        ];
    }
}
