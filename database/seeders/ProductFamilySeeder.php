<?php

namespace Database\Seeders;

use App\Models\ProductFamily;
use Illuminate\Database\Seeder;

class ProductFamilySeeder extends Seeder
{
    public function run(): void
    {
        $families = [
            ['name' => 'Services', 'description' => 'Service products'],
            ['name' => 'Physical Goods', 'description' => 'Physical products'],
            ['name' => 'Digital Products', 'description' => 'Digital downloads and licenses'],
        ];

        foreach ($families as $family) {
            ProductFamily::firstOrCreate(
                ['name' => $family['name']],
                $family
            );
        }
    }
}
