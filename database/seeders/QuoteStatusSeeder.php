<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuoteStatus;

class QuoteStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'draft', 'name' => 'Draft'],
            ['code' => 'sent', 'name' => 'Sent'],
            ['code' => 'viewed', 'name' => 'Viewed'],
            ['code' => 'approved', 'name' => 'Approved'],
            ['code' => 'rejected', 'name' => 'Rejected'],
            ['code' => 'expired', 'name' => 'Expired'],
        ];

        foreach ($statuses as $status) {
            QuoteStatus::create($status);
        }
    }
}
