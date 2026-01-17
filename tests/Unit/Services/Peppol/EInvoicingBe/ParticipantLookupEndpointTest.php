<?php

namespace Tests\Unit\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBe\ParticipantLookupEndpoint;
use App\Services\Peppol\EInvoicingBeClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ParticipantLookupEndpoint::class)]
class ParticipantLookupEndpointTest extends TestCase
{
    private EInvoicingBeClient $mockClient;
    private ParticipantLookupEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(EInvoicingBeClient::class);
        $this->endpoint = new ParticipantLookupEndpoint($this->mockClient);
    }

    #[Test]
    public function it_looks_up_participant(): void
    {
        /* Arrange */
        $participantId = '0208:BE0123456789';
        $expectedResponse = [
            'participant_id' => $participantId,
            'registered' => true,
            'company_name' => 'Belgian Company BVBA',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/participants/{$participantId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->lookupParticipant($participantId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['registered']);
    }

    #[Test]
    public function it_gets_belgian_endpoint(): void
    {
        /* Arrange */
        $vatNumber = 'BE0987654321';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'endpoint_url' => 'https://ap.einvoicing.be',
            'supports_peppol' => true,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/participants/belgian/{$vatNumber}/endpoint")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getBelgianEndpoint($vatNumber);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_handles_unregistered_participant(): void
    {
        /* Arrange */
        $participantId = '0208:BE0000000000';
        $expectedResponse = [
            'participant_id' => $participantId,
            'registered' => false,
            'message' => 'Participant not found in Belgian Peppol network',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/participants/{$participantId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->lookupParticipant($participantId);

        /* Assert */
        $this->assertFalse($response['registered']);
    }

    #[Test]
    public function it_gets_belgian_endpoint_with_capabilities(): void
    {
        /* Arrange */
        $vatNumber = 'BE0555555555';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'endpoint_url' => 'https://ap.test.be',
            'supports_peppol' => true,
            'capabilities' => ['invoice', 'credit-note', 'application-response'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/participants/belgian/{$vatNumber}/endpoint")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getBelgianEndpoint($vatNumber);

        /* Assert */
        $this->assertTrue($response['supports_peppol']);
        $this->assertNotEmpty($response['capabilities']);
    }

    #[Test]
    public function it_handles_endpoint_not_found(): void
    {
        /* Arrange */
        $vatNumber = 'BE0111111111';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'supports_peppol' => false,
            'error' => 'No Peppol endpoint registered for this VAT number',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/participants/belgian/{$vatNumber}/endpoint")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getBelgianEndpoint($vatNumber);

        /* Assert */
        $this->assertFalse($response['supports_peppol']);
        $this->assertNotEmpty($response['error']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
