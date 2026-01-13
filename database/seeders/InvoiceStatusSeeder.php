<?php

namespace Database\Seeders;

use App\Models\InvoiceStatus;
use Illuminate\Database\Seeder;

class InvoiceStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Draft', 'label' => 'draft'],
            ['name' => 'Sent', 'label' => 'sent'],
            ['name' => 'Viewed', 'label' => 'viewed'],
            ['name' => 'Approved', 'label' => 'approved'],
            ['name' => 'Rejected', 'label' => 'rejected'],
            ['name' => 'Paid', 'label' => 'paid'],
        ];

        foreach ($statuses as $status) {
            InvoiceStatus::firstOrCreate($status);
        }
    }
}
