<?php

namespace Tests\Unit\Services\Peppol;

use App\Enums\PeppolProvider;
use App\Services\Http\ApiClient;
use App\Services\Peppol\LetsPeppolClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(LetsPeppolClient::class)]
class LetsPeppolClientTest extends TestCase
{
    private ApiClient $mockApiClient;
    private LetsPeppolClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockApiClient = Mockery::mock(ApiClient::class);
        
        // Mock the configuration methods
        $this->mockApiClient->shouldReceive('setBaseUrl')
            ->once()
            ->with(PeppolProvider::LETSPEPPOL->getBaseUrl());
            
        $this->mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return array_key_exists('X-API-Key', $headers) &&
                       isset($headers['Content-Type']) &&
                       isset($headers['Accept']) &&
                       $headers['Content-Type'] === 'application/json' &&
                       $headers['Accept'] === 'application/json';
            }));
        
        $this->client = new LetsPeppolClient($this->mockApiClient);
    }

    #[Test]
    public function it_configures_base_url_on_construction(): void
    {
        /* Assert - handled in setUp by shouldReceive */
        $this->assertTrue(true);
    }

    #[Test]
    public function it_configures_headers_with_x_api_key_on_construction(): void
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
        $expectedResponse = ['invoice_id' => 'inv-789', 'status' => 'sent'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/v1/invoices', ['document' => '<Invoice/>'])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('POST', '/v1/invoices', ['document' => '<Invoice/>']);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_get_request(): void
    {
        /* Arrange */
        $expectedResponse = ['invoice_id' => 'inv-789', 'status' => 'delivered'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('GET', '/v1/invoices/inv-789/status', [])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('GET', '/v1/invoices/inv-789/status');

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_post_request_with_data(): void
    {
        /* Arrange */
        $data = [
            'participant_id' => '0088:1234567890123',
            'document_type' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
        ];
        
        $expectedResponse = ['valid' => true, 'endpoint' => 'https://ap.example.com'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/v1/participants/validate', $data)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('POST', '/v1/participants/validate', $data);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_put_request(): void
    {
        /* Arrange */
        $data = ['status' => 'cancelled'];
        $expectedResponse = ['invoice_id' => 'inv-789', 'status' => 'cancelled'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('PUT', '/v1/invoices/inv-789', $data)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('PUT', '/v1/invoices/inv-789', $data);

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
            ->with('DELETE', '/v1/invoices/inv-789', [])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('DELETE', '/v1/invoices/inv-789');

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_sets_x_api_key_header_from_config(): void
    {
        /* Arrange */
        config(['peppol.providers.letspeppol.api_key' => 'letspeppol-key-xyz']);
        
        $mockApiClient = Mockery::mock(ApiClient::class);
        
        $mockApiClient->shouldReceive('setBaseUrl')->once();
        
        $mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return $headers['X-API-Key'] === 'letspeppol-key-xyz';
            }));

        /* Act */
        new LetsPeppolClient($mockApiClient);

        /* Assert - handled by shouldReceive */
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
