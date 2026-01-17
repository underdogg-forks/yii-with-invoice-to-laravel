<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\ValidationServiceEndpoint;
use App\Services\Peppol\LetsPeppolClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ValidationServiceEndpoint::class)]
class ValidationServiceEndpointTest extends TestCase
{
    private LetsPeppolClient $mockClient;
    private ValidationServiceEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(LetsPeppolClient::class);
        $this->endpoint = new ValidationServiceEndpoint($this->mockClient);
    }

    #[Test]
    public function it_validates_invoice(): void
    {
        /* Arrange */
        $invoiceData = ['document' => '<Invoice/>'];
        $expectedResponse = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/validation/invoice', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateInvoice($invoiceData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['valid']);
    }

    #[Test]
    public function it_checks_compliance_with_default_specification(): void
    {
        /* Arrange */
        $xmlContent = '<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"/>';
        $expectedResponse = [
            'compliant' => true,
            'specification' => 'bis3',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/validation/compliance', [
                'xml' => $xmlContent,
                'specification' => 'bis3',
            ])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->checkCompliance($xmlContent);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['compliant']);
    }

    #[Test]
    public function it_checks_compliance_with_custom_specification(): void
    {
        /* Arrange */
        $xmlContent = '<Invoice/>';
        $specification = 'peppol-bis-3.0';
        
        $expectedResponse = [
            'compliant' => true,
            'specification' => $specification,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/validation/compliance', [
                'xml' => $xmlContent,
                'specification' => $specification,
            ])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->checkCompliance($xmlContent, $specification);

        /* Assert */
        $this->assertEquals($specification, $response['specification']);
    }

    #[Test]
    public function it_returns_validation_errors(): void
    {
        /* Arrange */
        $invoiceData = ['document' => '<InvalidInvoice/>'];
        $expectedResponse = [
            'valid' => false,
            'errors' => ['Missing required field: InvoiceNumber'],
            'warnings' => ['Optional field missing: PaymentMeans'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/validation/invoice', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateInvoice($invoiceData);

        /* Assert */
        $this->assertFalse($response['valid']);
        $this->assertNotEmpty($response['errors']);
    }

    #[Test]
    public function it_returns_compliance_violations(): void
    {
        /* Arrange */
        $xmlContent = '<Invoice/>';
        $expectedResponse = [
            'compliant' => false,
            'violations' => ['Invalid document structure'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/validation/compliance', Mockery::any())
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->checkCompliance($xmlContent);

        /* Assert */
        $this->assertFalse($response['compliant']);
        $this->assertNotEmpty($response['violations']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
