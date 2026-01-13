<?php

namespace Database\Factories;

use App\Models\SalesOrder;
use App\Models\Client;
use App\Models\User;
use App\Models\Quote;
use App\Models\SalesOrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 10000);
        $taxTotal = $subtotal * 0.21; // 21% tax
        $discountTotal = $this->faker->randomFloat(2, 0, $subtotal * 0.1);
        $total = $subtotal + $taxTotal - $discountTotal;

        return [
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
            'quote_id' => null,
            'sales_order_status_id' => SalesOrderStatus::factory()->pending(),
            'so_number' => 'SO-' . $this->faker->unique()->numberBetween(1000, 9999),
            'reference' => $this->faker->optional()->text(50),
            'order_date' => $this->faker->date(),
            'delivery_date' => $this->faker->optional()->dateTimeBetween('+1 week', '+2 months')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'discount_total' => $discountTotal,
            'total' => $total,
            'notes' => $this->faker->optional()->paragraph(),
            'terms' => $this->faker->optional()->paragraph(),
            'url_key' => Str::random(32),
        ];
    }

    public function fromQuote(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_id' => Quote::factory(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'sales_order_status_id' => SalesOrderStatus::factory()->pending(),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'sales_order_status_id' => SalesOrderStatus::factory()->confirmed(),
            'confirmed_at' => now(),
            'confirmed_by' => User::factory(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'sales_order_status_id' => SalesOrderStatus::factory()->completed(),
            'confirmed_at' => now()->subDays(5),
            'confirmed_by' => User::factory(),
            'processing_at' => now()->subDays(3),
            'completed_at' => now(),
            'completed_by' => User::factory(),
        ]);
    }
}
