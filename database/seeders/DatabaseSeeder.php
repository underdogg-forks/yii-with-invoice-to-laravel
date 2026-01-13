<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomFieldSeeder::class,
            ClientSeeder::class,
            UnitSeeder::class,
            InvoiceSeeder::class,
            ClientPeppolSeeder::class,
            UnitPeppolSeeder::class,
            PaymentPeppolSeeder::class,
            InvoiceStatusSeeder::class,
            InvoiceNumberingSeeder::class,
            TaxRateSeeder::class,
            ProductFamilySeeder::class,
        ]);
    }
}
