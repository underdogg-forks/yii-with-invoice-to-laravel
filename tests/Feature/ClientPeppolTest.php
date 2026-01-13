<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientPeppol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientPeppolTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_client_peppol(): void
    {
        $client = Client::factory()->create();
        
        $clientPeppol = ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'endpointid' => 'test@example.com',
            'buyer_reference' => 'REF-001',
        ]);

        $this->assertDatabaseHas('client_peppol', [
            'client_id' => $client->id,
            'endpointid' => 'test@example.com',
            'buyer_reference' => 'REF-001',
        ]);
    }

    public function test_client_peppol_belongs_to_client(): void
    {
        $clientPeppol = ClientPeppol::factory()->create();

        $this->assertInstanceOf(Client::class, $clientPeppol->client);
    }

    public function test_can_update_client_peppol(): void
    {
        $clientPeppol = ClientPeppol::factory()->create([
            'buyer_reference' => 'OLD-REF',
        ]);

        $clientPeppol->update(['buyer_reference' => 'NEW-REF']);

        $this->assertDatabaseHas('client_peppol', [
            'id' => $clientPeppol->id,
            'buyer_reference' => 'NEW-REF',
        ]);
    }

    public function test_can_delete_client_peppol(): void
    {
        $clientPeppol = ClientPeppol::factory()->create();
        $id = $clientPeppol->id;

        $clientPeppol->delete();

        $this->assertDatabaseMissing('client_peppol', [
            'id' => $id,
        ]);
    }
}
