<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\AcknowledgmentEndpoint;
use App\Services\Peppol\PeppyrusClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(AcknowledgmentEndpoint::class)]
class AcknowledgmentEndpointTest extends TestCase
{
    private PeppyrusClient $mockClient;
    private AcknowledgmentEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(PeppyrusClient::class);
        $this->endpoint = new AcknowledgmentEndpoint($this->mockClient);
    }

    #[Test]
    public function it_gets_acknowledgment(): void
    {
        /* Arrange */
        $transmissionId = 'trans-123';
        $expectedResponse = [
            'transmission_id' => $transmissionId,
            'acknowledged' => true,
            'acknowledged_at' => '2024-01-15T11:00:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/transmissions/{$transmissionId}/acknowledgment")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getAcknowledgment($transmissionId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['acknowledged']);
    }

    #[Test]
    public function it_gets_acknowledgment_details(): void
    {
        /* Arrange */
        $acknowledgmentId = 'ack-456';
        $expectedResponse = [
            'acknowledgment_id' => $acknowledgmentId,
            'status' => 'received',
            'type' => 'business_acknowledgment',
            'details' => ['message' => 'Invoice accepted'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/acknowledgments/{$acknowledgmentId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getAcknowledgmentDetails($acknowledgmentId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_handles_pending_acknowledgment(): void
    {
        /* Arrange */
        $transmissionId = 'trans-pending';
        $expectedResponse = [
            'transmission_id' => $transmissionId,
            'acknowledged' => false,
            'status' => 'pending',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/transmissions/{$transmissionId}/acknowledgment")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getAcknowledgment($transmissionId);

        /* Assert */
        $this->assertFalse($response['acknowledged']);
        $this->assertEquals('pending', $response['status']);
    }

    #[Test]
    public function it_handles_negative_acknowledgment(): void
    {
        /* Arrange */
        $acknowledgmentId = 'ack-negative';
        $expectedResponse = [
            'acknowledgment_id' => $acknowledgmentId,
            'status' => 'rejected',
            'type' => 'business_acknowledgment',
            'reason' => 'Invalid invoice data',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/acknowledgments/{$acknowledgmentId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getAcknowledgmentDetails($acknowledgmentId);

        /* Assert */
        $this->assertEquals('rejected', $response['status']);
        $this->assertNotEmpty($response['reason']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
