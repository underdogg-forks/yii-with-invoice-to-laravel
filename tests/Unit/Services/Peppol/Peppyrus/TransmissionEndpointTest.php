<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\TransmissionEndpoint;
use App\Services\Peppol\PeppyrusClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(TransmissionEndpoint::class)]
class TransmissionEndpointTest extends TestCase
{
    private PeppyrusClient $mockClient;
    private TransmissionEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(PeppyrusClient::class);
        $this->endpoint = new TransmissionEndpoint($this->mockClient);
    }

    #[Test]
    public function it_transmits_document(): void
    {
        /* Arrange */
        $documentData = [
            'document' => '<Invoice/>',
            'recipient' => '0088:1234567890123',
        ];
        
        $expectedResponse = [
            'transmission_id' => 'trans-123',
            'status' => 'transmitted',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/transmissions', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->transmitDocument($documentData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_transmission_status(): void
    {
        /* Arrange */
        $transmissionId = 'trans-456';
        $expectedResponse = [
            'transmission_id' => $transmissionId,
            'status' => 'delivered',
            'updated_at' => '2024-01-15T10:30:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/transmissions/{$transmissionId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getTransmissionStatus($transmissionId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_retries_transmission(): void
    {
        /* Arrange */
        $transmissionId = 'trans-retry';
        $expectedResponse = [
            'transmission_id' => $transmissionId,
            'status' => 'retrying',
            'retry_attempt' => 1,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, "/api/transmissions/{$transmissionId}/retry")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->retryTransmission($transmissionId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_transmits_document_with_metadata(): void
    {
        /* Arrange */
        $documentData = [
            'document' => '<Invoice/>',
            'recipient' => '0088:9999999999999',
            'metadata' => ['priority' => 'high'],
        ];
        
        $expectedResponse = ['transmission_id' => 'trans-meta'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/transmissions', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->transmitDocument($documentData);

        /* Assert */
        $this->assertEquals('trans-meta', $response['transmission_id']);
    }

    #[Test]
    public function it_handles_pending_transmission_status(): void
    {
        /* Arrange */
        $transmissionId = 'trans-pending';
        $expectedResponse = ['transmission_id' => $transmissionId, 'status' => 'pending'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/transmissions/{$transmissionId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getTransmissionStatus($transmissionId);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
    }

    #[Test]
    public function it_handles_failed_transmission_status(): void
    {
        /* Arrange */
        $transmissionId = 'trans-failed';
        $expectedResponse = [
            'transmission_id' => $transmissionId,
            'status' => 'failed',
            'error' => 'Network timeout',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/transmissions/{$transmissionId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getTransmissionStatus($transmissionId);

        /* Assert */
        $this->assertEquals('failed', $response['status']);
        $this->assertNotEmpty($response['error']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
