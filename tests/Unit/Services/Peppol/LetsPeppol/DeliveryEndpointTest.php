<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\DeliveryEndpoint;
use App\Services\Peppol\LetsPeppolClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(DeliveryEndpoint::class)]
class DeliveryEndpointTest extends TestCase
{
    private LetsPeppolClient $mockClient;
    private DeliveryEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(LetsPeppolClient::class);
        $this->endpoint = new DeliveryEndpoint($this->mockClient);
    }

    #[Test]
    public function it_gets_delivery_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-123';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'delivered',
            'delivered_at' => '2024-01-15T10:30:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/deliveries/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($invoiceId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_delivery_report(): void
    {
        /* Arrange */
        $invoiceId = 'inv-456';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'events' => [
                ['event' => 'sent', 'timestamp' => '2024-01-15T09:00:00Z'],
                ['event' => 'delivered', 'timestamp' => '2024-01-15T10:30:00Z'],
            ],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/deliveries/{$invoiceId}/report")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryReport($invoiceId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertCount(2, $response['events']);
    }

    #[Test]
    public function it_handles_pending_delivery_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-pending';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'pending',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/deliveries/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($invoiceId);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
    }

    #[Test]
    public function it_handles_failed_delivery_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-failed';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'failed',
            'error' => 'Recipient endpoint unavailable',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/deliveries/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($invoiceId);

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
