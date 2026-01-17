<?php

namespace Tests\Unit\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBe\ComplianceCheckEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeEInvoicingBeClient;
use Tests\PeppolTestCase;

#[CoversClass(ComplianceCheckEndpoint::class)]
class ComplianceCheckEndpointTest extends PeppolTestCase
{
    private FakeEInvoicingBeClient $fakeClient;
    private ComplianceCheckEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeEInvoicingBeClient();
        $this->endpoint = new ComplianceCheckEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_checks_belgian_compliance(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('einvoicing_be', 'compliance_check');
        $invoiceData = $fixture['request'];
        $expectedResponse = $fixture['response'];
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->checkBelgianCompliance($invoiceData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['compliant']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v1/compliance/check'));
    }

    #[Test]
    public function it_validates_structure(): void
    {
        /* Arrange */
        $xmlContent = '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"/>';
        $expectedResponse = [
            'valid' => true,
            'structure_errors' => [],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/compliance/validate-structure', ['xml' => $xmlContent])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateStructure($xmlContent);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['valid']);
    }

    #[Test]
    public function it_returns_belgian_compliance_violations(): void
    {
        /* Arrange */
        $invoiceData = [
            'document' => '<Invoice/>',
            'vat_number' => 'BE0000000000',
        ];
        
        $expectedResponse = [
            'compliant' => false,
            'violations' => [
                'Invalid Belgian VAT number format',
                'Missing structured communication reference',
            ],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/compliance/check', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->checkBelgianCompliance($invoiceData);

        /* Assert */
        $this->assertFalse($response['compliant']);
        $this->assertNotEmpty($response['violations']);
    }

    #[Test]
    public function it_returns_structure_validation_errors(): void
    {
        /* Arrange */
        $xmlContent = '<InvalidInvoice/>';
        $expectedResponse = [
            'valid' => false,
            'structure_errors' => [
                'Missing required namespace',
                'Invalid root element',
            ],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/compliance/validate-structure', ['xml' => $xmlContent])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateStructure($xmlContent);

        /* Assert */
        $this->assertFalse($response['valid']);
        $this->assertCount(2, $response['structure_errors']);
    }

    #[Test]
    public function it_checks_belgian_specific_requirements(): void
    {
        /* Arrange */
        $invoiceData = [
            'document' => '<Invoice/>',
            'vat_number' => 'BE0987654321',
            'structured_communication' => '+++123/4567/89012+++',
        ];
        
        $expectedResponse = [
            'compliant' => true,
            'belgian_requirements_met' => true,
            'structured_communication_valid' => true,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/compliance/check', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->checkBelgianCompliance($invoiceData);

        /* Assert */
        $this->assertTrue($response['structured_communication_valid']);
    }

}
