<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\DocumentsEndpoint;
use App\Services\Peppol\StoreCoveClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(DocumentsEndpoint::class)]
class DocumentsEndpointTest extends TestCase
{
    private StoreCoveClient $mockClient;
    private DocumentsEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(StoreCoveClient::class);
        $this->endpoint = new DocumentsEndpoint($this->mockClient);
    }

    #[Test]
    public function it_submits_document_successfully(): void
    {
        /* Arrange */
        $documentData = [
            'legal_entity_id' => 123,
            'document' => '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"/>',
        ];
        
        $expectedResponse = [
            'guid' => 'doc-guid-123',
            'status' => 'submitted',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/document_submissions', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->submitDocument($documentData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_document_status(): void
    {
        /* Arrange */
        $documentId = 'doc-guid-456';
        $expectedResponse = [
            'guid' => $documentId,
            'status' => 'delivered',
            'updated_at' => '2024-01-15T10:30:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/document_submissions/{$documentId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDocumentStatus($documentId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_full_document(): void
    {
        /* Arrange */
        $documentId = 'doc-guid-789';
        $expectedResponse = [
            'guid' => $documentId,
            'document' => '<Invoice>...</Invoice>',
            'metadata' => ['sender' => 'Company A'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/document_submissions/{$documentId}/document")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getDocument($documentId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_cancels_document(): void
    {
        /* Arrange */
        $documentId = 'doc-guid-cancel';
        $expectedResponse = [
            'guid' => $documentId,
            'status' => 'cancelled',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::DELETE->value, "/api/v2/document_submissions/{$documentId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->cancelDocument($documentId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_submits_document_with_routing_information(): void
    {
        /* Arrange */
        $documentData = [
            'legal_entity_id' => 456,
            'document' => '<Invoice/>',
            'routing' => [
                'eIdentifiers' => [
                    ['scheme' => '0088', 'id' => '1234567890123']
                ]
            ],
        ];
        
        $expectedResponse = ['guid' => 'doc-routed-123'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/document_submissions', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->submitDocument($documentData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_handles_document_with_attachments(): void
    {
        /* Arrange */
        $documentData = [
            'legal_entity_id' => 789,
            'document' => '<Invoice/>',
            'attachments' => [
                ['filename' => 'attachment.pdf', 'content' => 'base64content']
            ],
        ];
        
        $expectedResponse = ['guid' => 'doc-attach-456'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/document_submissions', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->submitDocument($documentData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
