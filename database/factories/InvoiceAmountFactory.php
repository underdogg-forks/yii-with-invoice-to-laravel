<?php

namespace Database\Factories;

use App\Models\InvoiceAmount;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceAmountFactory extends Factory
{
    protected $model = InvoiceAmount::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $taxTotal = $subtotal * 0.21; // 21% tax
        $discount = $this->faker->optional(0.3)->randomFloat(2, 0, $subtotal * 0.1);
        $total = $subtotal + $taxTotal - ($discount ?? 0);
        
        return [
            'invoice_id' => Invoice::factory(),
            'item_subtotal' => $subtotal,
            'item_tax_total' => $taxTotal,
            'tax_total' => $taxTotal,
            'discount' => $discount ?? 0,
            'total' => $total,
            'paid' => $this->faker->optional(0.5)->randomFloat(2, 0, $total),
            'balance' => $total,
        ];
    }
}
