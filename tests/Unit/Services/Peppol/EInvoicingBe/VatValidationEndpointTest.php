<?php

namespace Tests\Unit\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBe\VatValidationEndpoint;
use App\Services\Peppol\EInvoicingBeClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(VatValidationEndpoint::class)]
class VatValidationEndpointTest extends TestCase
{
    private EInvoicingBeClient $mockClient;
    private VatValidationEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(EInvoicingBeClient::class);
        $this->endpoint = new VatValidationEndpoint($this->mockClient);
    }

    #[Test]
    public function it_validates_vat_number(): void
    {
        /* Arrange */
        $vatNumber = 'BE0123456789';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'valid' => true,
            'company_name' => 'Test Company BVBA',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/vat/validate', ['vat_number' => $vatNumber])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateVatNumber($vatNumber);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['valid']);
    }

    #[Test]
    public function it_gets_vat_details(): void
    {
        /* Arrange */
        $vatNumber = 'BE0987654321';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'company_name' => 'Example Corp NV',
            'address' => [
                'street' => 'Rue Example 123',
                'city' => 'Brussels',
                'postal_code' => '1000',
            ],
            'active' => true,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/vat/{$vatNumber}/details")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getVatDetails($vatNumber);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_handles_invalid_vat_number(): void
    {
        /* Arrange */
        $vatNumber = 'BE0000000000';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'valid' => false,
            'error' => 'VAT number not found in Belgian registry',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/vat/validate', ['vat_number' => $vatNumber])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateVatNumber($vatNumber);

        /* Assert */
        $this->assertFalse($response['valid']);
        $this->assertNotEmpty($response['error']);
    }

    #[Test]
    public function it_validates_vat_with_checksum(): void
    {
        /* Arrange */
        $vatNumber = 'BE0477472701';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'valid' => true,
            'checksum_valid' => true,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/vat/validate', ['vat_number' => $vatNumber])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->validateVatNumber($vatNumber);

        /* Assert */
        $this->assertTrue($response['valid']);
        $this->assertTrue($response['checksum_valid']);
    }

    #[Test]
    public function it_gets_vat_details_with_inactive_status(): void
    {
        /* Arrange */
        $vatNumber = 'BE0111111111';
        $expectedResponse = [
            'vat_number' => $vatNumber,
            'company_name' => 'Inactive Company',
            'active' => false,
            'deactivation_date' => '2023-12-31',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/vat/{$vatNumber}/details")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getVatDetails($vatNumber);

        /* Assert */
        $this->assertFalse($response['active']);
        $this->assertNotEmpty($response['deactivation_date']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
