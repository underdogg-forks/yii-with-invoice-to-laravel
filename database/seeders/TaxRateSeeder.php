<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['name' => 'VAT 21%', 'percent' => 21.00, 'is_active' => true],
            ['name' => 'VAT 9%', 'percent' => 9.00, 'is_active' => true],
            ['name' => 'VAT 0%', 'percent' => 0.00, 'is_active' => true],
        ];

        foreach ($rates as $rate) {
            TaxRate::firstOrCreate(
                ['name' => $rate['name']],
                $rate
            );
        }
    }
}
