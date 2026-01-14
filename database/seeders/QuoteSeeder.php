<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;
use App\Models\QuoteStatus;
use App\Models\Client;
use App\Models\User;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::first() ?? Client::factory()->create();
        $user = User::first() ?? User::factory()->create();

        $draftStatus = QuoteStatus::where('code', 'draft')->first();
        $sentStatus = QuoteStatus::where('code', 'sent')->first();
        $approvedStatus = QuoteStatus::where('code', 'approved')->first();

        // Draft quote
        Quote::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'quote_status_id' => $draftStatus->id,
            'quote_number' => 'Q-2026-001',
        ]);

        // Sent quote
        Quote::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'quote_status_id' => $sentStatus->id,
            'quote_number' => 'Q-2026-002',
            'sent_at' => now()->subDays(3),
        ]);

        // Approved quote
        Quote::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'quote_status_id' => $approvedStatus->id,
            'quote_number' => 'Q-2026-003',
            'sent_at' => now()->subDays(5),
            'viewed_at' => now()->subDays(4),
            'approved_at' => now()->subDays(2),
            'approved_by' => $user->id,
        ]);
    }
}
