<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\ValidationEndpoint;
use App\Services\Peppol\StoreCoveClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ValidationEndpoint::class)]
class ValidationEndpointTest extends TestCase
{
    private StoreCoveClient $mockClient;
    private ValidationEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(StoreCoveClient::class);
        $this->endpoint = new ValidationEndpoint($this->mockClient);
    }

    #[Test]
    public function it_validates_document(): void
    {
        /* Arrange */
        $documentData = ['document' => '<Invoice/>'];
        $expectedResponse = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/validation/document', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateDocument($documentData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_validates_participant(): void
    {
        /* Arrange */
        $participantId = '0088:1234567890123';
        $documentType = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
        
        $expectedResponse = [
            'valid' => true,
            'participant_id' => $participantId,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, '/api/v2/validation/participant', [
                'participant_id' => $participantId,
                'document_type' => $documentType,
            ])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateParticipant($participantId, $documentType);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_validates_syntax(): void
    {
        /* Arrange */
        $xmlContent = '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"></Invoice>';
        $expectedResponse = [
            'valid' => true,
            'syntax_errors' => [],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/validation/syntax', ['xml' => $xmlContent])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateSyntax($xmlContent);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_returns_validation_errors(): void
    {
        /* Arrange */
        $documentData = ['document' => '<InvalidInvoice/>'];
        $expectedResponse = [
            'valid' => false,
            'errors' => ['Missing required element: cbc:ID'],
            'warnings' => ['Recommended element missing: cbc:Note'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/validation/document', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateDocument($documentData);

        /* Assert */
        $this->assertFalse($response['valid']);
        $this->assertNotEmpty($response['errors']);
    }

    #[Test]
    public function it_handles_invalid_participant(): void
    {
        /* Arrange */
        $participantId = '9999:0000000000000';
        $documentType = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
        
        $expectedResponse = [
            'valid' => false,
            'error' => 'Participant not found in network',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, '/api/v2/validation/participant', Mockery::any())
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateParticipant($participantId, $documentType);

        /* Assert */
        $this->assertFalse($response['valid']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
