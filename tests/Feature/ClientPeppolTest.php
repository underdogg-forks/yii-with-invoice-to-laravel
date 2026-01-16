<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientPeppol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ClientPeppol::class)]
class ClientPeppolTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_client_peppol(): void
    {
        /* Arrange */
        $client = Client::factory()->create();
        
        $payload = [
            'client_id' => $client->id,
            'endpointid' => 'test@example.com',
            'buyer_reference' => 'REF-001',
        ];

        /* Act */
        $clientPeppol = ClientPeppol::factory()->create($payload);

        /* Assert */
        $this->assertDatabaseHas('client_peppol', $payload);
        $this->assertNotNull($clientPeppol->id);
    }

    #[Test]
    public function it_has_client_relationship(): void
    {
        /* Arrange */
        $clientPeppol = ClientPeppol::factory()->create();

        /* Act */
        $client = $clientPeppol->client;

        /* Assert */
        $this->assertInstanceOf(Client::class, $client);
    }

    #[Test]
    public function it_updates_client_peppol(): void
    {
        /* Arrange */
        $clientPeppol = ClientPeppol::factory()->create([
            'buyer_reference' => 'OLD-REF',
        ]);

        $payload = ['buyer_reference' => 'NEW-REF'];

        /* Act */
        $clientPeppol->update($payload);

        /* Assert */
        $this->assertDatabaseHas('client_peppol', [
            'id' => $clientPeppol->id,
            'buyer_reference' => 'NEW-REF',
        ]);
    }

    #[Test]
    public function it_deletes_client_peppol(): void
    {
        /* Arrange */
        $clientPeppol = ClientPeppol::factory()->create();
        $id = $clientPeppol->id;

        /* Act */
        $clientPeppol->delete();

        /* Assert */
        $this->assertDatabaseMissing('client_peppol', [
            'id' => $id,
        ]);
    }
}
