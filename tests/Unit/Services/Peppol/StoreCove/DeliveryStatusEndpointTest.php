<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\DeliveryStatusEndpoint;
use App\Services\Peppol\StoreCoveClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(DeliveryStatusEndpoint::class)]
class DeliveryStatusEndpointTest extends TestCase
{
    private StoreCoveClient $mockClient;
    private DeliveryStatusEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(StoreCoveClient::class);
        $this->endpoint = new DeliveryStatusEndpoint($this->mockClient);
    }

    #[Test]
    public function it_gets_delivery_status(): void
    {
        /* Arrange */
        $documentId = 'doc-123';
        $expectedResponse = [
            'status' => 'delivered',
            'delivered_at' => '2024-01-15T10:30:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/document_submissions/{$documentId}/delivery_status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($documentId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_delivery_history(): void
    {
        /* Arrange */
        $documentId = 'doc-456';
        $expectedResponse = [
            [
                'event' => 'submitted',
                'timestamp' => '2024-01-15T09:00:00Z',
            ],
            [
                'event' => 'delivered',
                'timestamp' => '2024-01-15T10:30:00Z',
            ],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/document_submissions/{$documentId}/delivery_history")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryHistory($documentId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_checks_recipient_acknowledgment(): void
    {
        /* Arrange */
        $documentId = 'doc-789';
        $expectedResponse = [
            'acknowledged' => true,
            'acknowledged_at' => '2024-01-15T11:00:00Z',
            'acknowledgment_type' => 'read',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/document_submissions/{$documentId}/acknowledgment")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->checkRecipientAcknowledgment($documentId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_handles_pending_delivery_status(): void
    {
        /* Arrange */
        $documentId = 'doc-pending';
        $expectedResponse = [
            'status' => 'pending',
            'last_updated' => '2024-01-15T09:15:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/document_submissions/{$documentId}/delivery_status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($documentId);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
    }

    #[Test]
    public function it_handles_failed_delivery_status(): void
    {
        /* Arrange */
        $documentId = 'doc-failed';
        $expectedResponse = [
            'status' => 'failed',
            'error' => 'Recipient not found',
            'failed_at' => '2024-01-15T10:00:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/document_submissions/{$documentId}/delivery_status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($documentId);

        /* Assert */
        $this->assertEquals('failed', $response['status']);
        $this->assertEquals('Recipient not found', $response['error']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
