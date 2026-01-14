<?php

namespace Database\Seeders;

use App\Models\InvoiceNumbering;
use Illuminate\Database\Seeder;

class InvoiceNumberingSeeder extends Seeder
{
    public function run(): void
    {
        InvoiceNumbering::firstOrCreate(
            ['name' => 'Default Invoice Numbering'],
            [
                'prefix' => 'INV',
                'next_number' => 1,
                'left_pad' => 4,
                'is_default' => true,
            ]
        );
    }
}
