<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\ComplianceEndpoint;
use App\Services\Peppol\PeppyrusClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ComplianceEndpoint::class)]
class ComplianceEndpointTest extends TestCase
{
    private PeppyrusClient $mockClient;
    private ComplianceEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(PeppyrusClient::class);
        $this->endpoint = new ComplianceEndpoint($this->mockClient);
    }

    #[Test]
    public function it_validates_compliance(): void
    {
        /* Arrange */
        $documentData = ['document' => '<Invoice/>'];
        $expectedResponse = [
            'compliant' => true,
            'errors' => [],
            'warnings' => [],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/compliance/validate', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateCompliance($documentData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['compliant']);
    }

    #[Test]
    public function it_gets_validation_report(): void
    {
        /* Arrange */
        $validationId = 'val-123';
        $expectedResponse = [
            'validation_id' => $validationId,
            'compliant' => true,
            'checked_at' => '2024-01-15T10:30:00Z',
            'rules_checked' => 150,
            'rules_passed' => 150,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/compliance/reports/{$validationId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getValidationReport($validationId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_returns_compliance_violations(): void
    {
        /* Arrange */
        $documentData = ['document' => '<InvalidInvoice/>'];
        $expectedResponse = [
            'compliant' => false,
            'errors' => ['Missing required element: cbc:ID'],
            'warnings' => ['Recommended element missing: cbc:Note'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/compliance/validate', $documentData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateCompliance($documentData);

        /* Assert */
        $this->assertFalse($response['compliant']);
        $this->assertNotEmpty($response['errors']);
    }

    #[Test]
    public function it_gets_detailed_validation_report(): void
    {
        /* Arrange */
        $validationId = 'val-detailed';
        $expectedResponse = [
            'validation_id' => $validationId,
            'compliant' => false,
            'rules_checked' => 150,
            'rules_passed' => 147,
            'rules_failed' => 3,
            'failed_rules' => [
                ['rule' => 'BR-01', 'message' => 'Invoice must have an ID'],
                ['rule' => 'BR-02', 'message' => 'Invoice must have an issue date'],
            ],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/compliance/reports/{$validationId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getValidationReport($validationId);

        /* Assert */
        $this->assertFalse($response['compliant']);
        $this->assertEquals(3, $response['rules_failed']);
        $this->assertNotEmpty($response['failed_rules']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
