<?php

namespace Tests\Unit\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBe\StatusTrackingEndpoint;
use App\Services\Peppol\EInvoicingBeClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(StatusTrackingEndpoint::class)]
class StatusTrackingEndpointTest extends TestCase
{
    private EInvoicingBeClient $mockClient;
    private StatusTrackingEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(EInvoicingBeClient::class);
        $this->endpoint = new StatusTrackingEndpoint($this->mockClient);
    }

    #[Test]
    public function it_tracks_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-be-123';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'delivered',
            'last_updated' => '2024-01-15T10:30:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/tracking/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->trackStatus($invoiceId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_status_history(): void
    {
        /* Arrange */
        $invoiceId = 'inv-be-456';
        $expectedResponse = [
            [
                'status' => 'submitted',
                'timestamp' => '2024-01-15T09:00:00Z',
            ],
            [
                'status' => 'validated',
                'timestamp' => '2024-01-15T09:15:00Z',
            ],
            [
                'status' => 'delivered',
                'timestamp' => '2024-01-15T10:30:00Z',
            ],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/tracking/{$invoiceId}/history")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getStatusHistory($invoiceId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertCount(3, $response);
    }

    #[Test]
    public function it_handles_pending_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-be-pending';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'pending',
            'message' => 'Awaiting validation',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/tracking/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->trackStatus($invoiceId);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
    }

    #[Test]
    public function it_handles_failed_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-be-failed';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'failed',
            'error' => 'Validation failed',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/tracking/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->trackStatus($invoiceId);

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
