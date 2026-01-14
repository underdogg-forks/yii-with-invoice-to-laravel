<?php

namespace Database\Seeders;

use App\Models\PaymentPeppol;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class PaymentPeppolSeeder extends Seeder
{
    public function run(): void
    {
        // Create Peppol payment data for the first 10 invoices
        $invoices = Invoice::limit(10)->get();
        
        foreach ($invoices as $invoice) {
            PaymentPeppol::factory()->create([
                'inv_id' => $invoice->id,
            ]);
        }
    }
}
