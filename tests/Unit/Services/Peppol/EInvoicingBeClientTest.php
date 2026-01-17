<?php

namespace Tests\Unit\Services\Peppol;

use App\Enums\PeppolProvider;
use App\Services\Http\ApiClient;
use App\Services\Peppol\EInvoicingBeClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(EInvoicingBeClient::class)]
class EInvoicingBeClientTest extends TestCase
{
    private ApiClient $mockApiClient;
    private EInvoicingBeClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockApiClient = Mockery::mock(ApiClient::class);
        
        // Mock the configuration methods
        $this->mockApiClient->shouldReceive('setBaseUrl')
            ->once()
            ->with(PeppolProvider::EINVOICING_BE->getBaseUrl());
            
        $this->mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return isset($headers['Authorization']) &&
                       isset($headers['X-API-Key']) &&
                       isset($headers['Content-Type']) &&
                       isset($headers['Accept']) &&
                       $headers['Content-Type'] === 'application/json' &&
                       $headers['Accept'] === 'application/json';
            }));
        
        $this->client = new EInvoicingBeClient($this->mockApiClient);
    }

    #[Test]
    public function it_configures_base_url_on_construction(): void
    {
        /* Assert - handled in setUp by shouldReceive */
        $this->assertTrue(true);
    }

    #[Test]
    public function it_configures_headers_with_bearer_and_api_key_on_construction(): void
    {
        /* Assert - handled in setUp by shouldReceive */
        $this->assertTrue(true);
    }

    #[Test]
    public function it_returns_api_client_instance(): void
    {
        /* Act */
        $apiClient = $this->client->getApiClient();

        /* Assert */
        $this->assertSame($this->mockApiClient, $apiClient);
    }

    #[Test]
    public function it_makes_request_using_api_client(): void
    {
        /* Arrange */
        $expectedResponse = ['submission_id' => 'sub-be-123', 'status' => 'accepted'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/api/v1/invoices/submit', ['invoice' => '<Invoice/>'])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('POST', '/api/v1/invoices/submit', ['invoice' => '<Invoice/>']);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_get_request(): void
    {
        /* Arrange */
        $expectedResponse = ['vat_number' => 'BE0123456789', 'valid' => true];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('GET', '/api/v1/vat/BE0123456789/details', [])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('GET', '/api/v1/vat/BE0123456789/details');

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_post_request_with_data(): void
    {
        /* Arrange */
        $data = ['vat_number' => 'BE0987654321'];
        $expectedResponse = ['valid' => true, 'company' => 'Test Company'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/api/v1/vat/validate', $data)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('POST', '/api/v1/vat/validate', $data);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_put_request(): void
    {
        /* Arrange */
        $data = ['status' => 'cancelled'];
        $expectedResponse = ['submission_id' => 'sub-be-456', 'status' => 'cancelled'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('PUT', '/api/v1/invoices/submissions/sub-be-456', $data)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('PUT', '/api/v1/invoices/submissions/sub-be-456', $data);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_delete_request(): void
    {
        /* Arrange */
        $expectedResponse = ['deleted' => true];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('DELETE', '/api/v1/invoices/submissions/sub-be-789', [])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('DELETE', '/api/v1/invoices/submissions/sub-be-789');

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_sets_authorization_headers_from_config(): void
    {
        /* Arrange */
        config([
            'peppol.providers.einvoicing_be.api_key' => 'be-api-key-123',
            'peppol.providers.einvoicing_be.bearer_token' => 'be-bearer-token-456',
        ]);
        
        $mockApiClient = Mockery::mock(ApiClient::class);
        
        $mockApiClient->shouldReceive('setBaseUrl')->once();
        
        $mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return $headers['Authorization'] === 'Bearer be-bearer-token-456' &&
                       $headers['X-API-Key'] === 'be-api-key-123';
            }));

        /* Act */
        new EInvoicingBeClient($mockApiClient);

        /* Assert - handled by shouldReceive */
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
