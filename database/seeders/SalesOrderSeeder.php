<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesOrder;
use App\Models\SalesOrderStatus;
use App\Models\Client;
use App\Models\User;

class SalesOrderSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::first() ?? Client::factory()->create();
        $user = User::first() ?? User::factory()->create();

        $pendingStatus = SalesOrderStatus::where('code', 'pending')->first();
        $confirmedStatus = SalesOrderStatus::where('code', 'confirmed')->first();
        $completedStatus = SalesOrderStatus::where('code', 'completed')->first();

        // Pending SO
        SalesOrder::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'sales_order_status_id' => $pendingStatus->id,
            'so_number' => 'SO-2026-001',
        ]);

        // Confirmed SO
        SalesOrder::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'sales_order_status_id' => $confirmedStatus->id,
            'so_number' => 'SO-2026-002',
            'confirmed_at' => now()->subDays(2),
            'confirmed_by' => $user->id,
        ]);

        // Completed SO
        SalesOrder::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'sales_order_status_id' => $completedStatus->id,
            'so_number' => 'SO-2026-003',
            'confirmed_at' => now()->subDays(10),
            'confirmed_by' => $user->id,
            'processing_at' => now()->subDays(7),
            'completed_at' => now()->subDays(2),
            'completed_by' => $user->id,
        ]);
    }
}
