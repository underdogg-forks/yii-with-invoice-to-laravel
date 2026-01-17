<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\ParticipantEndpoint;
use App\Services\Peppol\LetsPeppolClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ParticipantEndpoint::class)]
class ParticipantEndpointTest extends TestCase
{
    private LetsPeppolClient $mockClient;
    private ParticipantEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(LetsPeppolClient::class);
        $this->endpoint = new ParticipantEndpoint($this->mockClient);
    }

    #[Test]
    public function it_looks_up_participant(): void
    {
        /* Arrange */
        $participantId = '0088:1234567890123';
        $expectedResponse = [
            'participant_id' => $participantId,
            'name' => 'Test Company',
            'registered' => true,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/participants/{$participantId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->lookupParticipant($participantId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_participant_details(): void
    {
        /* Arrange */
        $participantId = '0088:9876543210987';
        $expectedResponse = [
            'participant_id' => $participantId,
            'name' => 'Example Corp',
            'endpoints' => ['https://ap.example.com'],
            'capabilities' => ['invoice', 'credit-note'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/participants/{$participantId}/details")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getParticipantDetails($participantId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_validates_endpoint(): void
    {
        /* Arrange */
        $participantId = '0088:5555555555555';
        $documentType = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
        
        $expectedResponse = [
            'valid' => true,
            'endpoint' => 'https://ap.test.com',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/participants/validate', [
                'participant_id' => $participantId,
                'document_type' => $documentType,
            ])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateEndpoint($participantId, $documentType);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_handles_unregistered_participant(): void
    {
        /* Arrange */
        $participantId = '0088:0000000000000';
        $expectedResponse = [
            'participant_id' => $participantId,
            'registered' => false,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/participants/{$participantId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->lookupParticipant($participantId);

        /* Assert */
        $this->assertFalse($response['registered']);
    }

    #[Test]
    public function it_validates_endpoint_for_credit_note(): void
    {
        /* Arrange */
        $participantId = '0088:7777777777777';
        $documentType = 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2';
        
        $expectedResponse = [
            'valid' => true,
            'endpoint' => 'https://ap.example.com',
            'supports_document_type' => true,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/participants/validate', Mockery::any())
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateEndpoint($participantId, $documentType);

        /* Assert */
        $this->assertTrue($response['valid']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
