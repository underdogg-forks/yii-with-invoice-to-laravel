<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\Client;
use App\Models\User;
use App\Models\QuoteStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 10000);
        $taxTotal = $subtotal * 0.21; // 21% tax
        $discountTotal = $this->faker->randomFloat(2, 0, $subtotal * 0.1);
        $total = $subtotal + $taxTotal - $discountTotal;

        return [
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
            'quote_status_id' => QuoteStatus::factory()->draft(),
            'quote_number' => 'Q-' . $this->faker->unique()->numberBetween(1000, 9999),
            'reference' => $this->faker->optional()->text(50),
            'quote_date' => $this->faker->date(),
            'expiry_date' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'discount_total' => $discountTotal,
            'total' => $total,
            'notes' => $this->faker->optional()->paragraph(),
            'terms' => $this->faker->optional()->paragraph(),
            'url_key' => Str::random(32),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_status_id' => QuoteStatus::factory()->draft(),
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_status_id' => QuoteStatus::factory()->sent(),
            'sent_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_status_id' => QuoteStatus::factory()->approved(),
            'sent_at' => now()->subDays(2),
            'viewed_at' => now()->subDay(),
            'approved_at' => now(),
            'approved_by' => User::factory(),
        ]);
    }
}
