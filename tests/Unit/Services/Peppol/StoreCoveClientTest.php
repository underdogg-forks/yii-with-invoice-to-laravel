<?php

namespace Tests\Unit\Services\Peppol;

use App\Enums\HttpMethod;
use App\Enums\PeppolProvider;
use App\Services\Http\ApiClient;
use App\Services\Peppol\StoreCoveClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(StoreCoveClient::class)]
class StoreCoveClientTest extends TestCase
{
    private ApiClient $mockApiClient;
    private StoreCoveClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockApiClient = Mockery::mock(ApiClient::class);
        
        // Mock the configuration methods
        $this->mockApiClient->shouldReceive('setBaseUrl')
            ->once()
            ->with(PeppolProvider::STORECOVE->getBaseUrl());
            
        $this->mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return isset($headers['Authorization']) &&
                       isset($headers['Content-Type']) &&
                       isset($headers['Accept']) &&
                       $headers['Content-Type'] === 'application/json' &&
                       $headers['Accept'] === 'application/json';
            }));
        
        $this->client = new StoreCoveClient($this->mockApiClient);
    }

    #[Test]
    public function it_configures_base_url_on_construction(): void
    {
        /* Assert - handled in setUp by shouldReceive */
        $this->assertTrue(true);
    }

    #[Test]
    public function it_configures_headers_with_bearer_token_on_construction(): void
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
        $expectedResponse = ['id' => 123, 'status' => 'submitted'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/documents', ['xml' => '<Invoice/>'])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('POST', '/documents', ['xml' => '<Invoice/>']);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_get_request(): void
    {
        /* Arrange */
        $expectedResponse = ['document_id' => 'doc-123', 'status' => 'delivered'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('GET', '/documents/doc-123', [])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('GET', '/documents/doc-123');

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_post_request_with_data(): void
    {
        /* Arrange */
        $data = [
            'legal_entity_id' => 123,
            'document' => '<Invoice/>',
        ];
        
        $expectedResponse = ['submission_id' => 'sub-456'];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/document_submissions', $data)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('POST', '/document_submissions', $data);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_makes_put_request(): void
    {
        /* Arrange */
        $data = ['name' => 'Updated Legal Entity'];
        $expectedResponse = ['id' => 789, 'updated' => true];
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('PUT', '/legal_entities/789', $data)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('PUT', '/legal_entities/789', $data);

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
            ->with('DELETE', '/webhooks/webhook-123', [])
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->client->request('DELETE', '/webhooks/webhook-123');

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_sets_authorization_header_from_config(): void
    {
        /* Arrange */
        config(['peppol.providers.storecove.api_key' => 'test-api-key-12345']);
        
        $mockApiClient = Mockery::mock(ApiClient::class);
        
        $mockApiClient->shouldReceive('setBaseUrl')->once();
        
        $mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return str_contains($headers['Authorization'], 'test-api-key-12345');
            }));

        /* Act */
        new StoreCoveClient($mockApiClient);

        /* Assert - handled by shouldReceive */
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
