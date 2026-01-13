<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $price = $this->faker->randomFloat(2, 10, 1000);
        $discount = $this->faker->optional(0.3)->randomFloat(2, 0, $price * $quantity * 0.2);
        
        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => Product::factory(),
            'tax_rate_id' => TaxRate::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'quantity' => $quantity,
            'price' => $price,
            'discount_amount' => $discount ?? 0,
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
