<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\AccessPointEndpoint;
use App\Services\Peppol\PeppyrusClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AccessPointEndpoint::class)]
class AccessPointEndpointTest extends TestCase
{
    private PeppyrusClient $mockClient;
    private AccessPointEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(PeppyrusClient::class);
        $this->endpoint = new AccessPointEndpoint($this->mockClient);
    }

    #[Test]
    public function it_queries_access_point(): void
    {
        /* Arrange */
        $participantId = '0088:1234567890123';
        $documentType = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
        
        $expectedResponse = [
            'participant_id' => $participantId,
            'access_point_url' => 'https://ap.example.com',
            'certificate' => 'cert-data',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, '/api/access-points/query', [
                'participant_id' => $participantId,
                'document_type' => $documentType,
            ])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->queryAccessPoint($participantId, $documentType);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_access_point_metadata(): void
    {
        /* Arrange */
        $accessPointId = 'ap-456';
        $expectedResponse = [
            'access_point_id' => $accessPointId,
            'url' => 'https://ap.test.com',
            'provider' => 'Test Provider',
            'capabilities' => ['invoice', 'credit-note'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/access-points/{$accessPointId}/metadata")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getAccessPointMetadata($accessPointId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_queries_access_point_for_credit_note(): void
    {
        /* Arrange */
        $participantId = '0088:9876543210987';
        $documentType = 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2';
        
        $expectedResponse = [
            'participant_id' => $participantId,
            'access_point_url' => 'https://ap.creditnote.com',
            'supports_document_type' => true,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, '/api/access-points/query', Mockery::any())
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->queryAccessPoint($participantId, $documentType);

        /* Assert */
        $this->assertTrue($response['supports_document_type']);
    }

    #[Test]
    public function it_handles_access_point_not_found(): void
    {
        /* Arrange */
        $participantId = '0088:0000000000000';
        $documentType = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
        
        $expectedResponse = [
            'participant_id' => $participantId,
            'found' => false,
            'error' => 'No access point registered for participant',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, '/api/access-points/query', Mockery::any())
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->queryAccessPoint($participantId, $documentType);

        /* Assert */
        $this->assertFalse($response['found']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
