<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesOrderStatus;

class SalesOrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'pending', 'name' => 'Pending'],
            ['code' => 'confirmed', 'name' => 'Confirmed'],
            ['code' => 'processing', 'name' => 'Processing'],
            ['code' => 'completed', 'name' => 'Completed'],
            ['code' => 'cancelled', 'name' => 'Cancelled'],
        ];

        foreach ($statuses as $status) {
            SalesOrderStatus::create($status);
        }
    }
}
