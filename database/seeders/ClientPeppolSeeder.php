<?php

namespace Database\Seeders;

use App\Models\ClientPeppol;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientPeppolSeeder extends Seeder
{
    public function run(): void
    {
        // Create Peppol data for the first 5 clients
        $clients = Client::limit(5)->get();
        
        foreach ($clients as $client) {
            ClientPeppol::factory()->create([
                'client_id' => $client->id,
            ]);
        }
    }
}
