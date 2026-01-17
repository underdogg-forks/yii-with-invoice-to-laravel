<?php

namespace Tests\Unit\Services\Peppol;

use App\Enums\PeppolProvider;
use App\Services\Http\ApiClient;
use App\Services\Peppol\PeppyrusClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PeppyrusClient::class)]
class PeppyrusClientTest extends TestCase
{
    private ApiClient $mockApiClient;
    private PeppyrusClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockApiClient = Mockery::mock(ApiClient::class);
        
        // Mock the configuration methods
        $this->mockApiClient->shouldReceive('setBaseUrl')
            ->once()
            ->with(PeppolProvider::PEPPYRUS->getBaseUrl());
            
        $this->mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return isset($headers['Content-Type']) &&
                       isset($headers['Accept']) &&
                       $headers['Content-Type'] === 'application/json' &&
                       $headers['Accept'] === 'application/json';
            }));
        
        $this->client = new PeppyrusClient($this->mockApiClient);
    }

    #[Test]
    public function it_configures_base_url_on_construction(): void
    {
        /* Assert - handled in setUp by shouldReceive */
        $this->assertTrue(true);
    }

    #[Test]
    public function it_configures_headers_on_construction(): void
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
    public function it_obtains_oauth2_access_token_on_first_request(): void
    {
        /* Arrange */
        config([
            'peppol.providers.peppyrus.client_id' => 'client-id-123',
            'peppol.providers.peppyrus.client_secret' => 'secret-abc',
        ]);
        
        // Mock OAuth token request
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => 'client-id-123',
                'client_secret' => 'secret-abc',
            ])
            ->andReturn([
                'access_token' => 'oauth-token-xyz',
                'expires_in' => 3600,
            ]);
        
        // Mock the actual request with Bearer token
        $this->mockApiClient->shouldReceive('setHeaders')
            ->once()
            ->with(Mockery::on(function ($headers) {
                return isset($headers['Authorization']) &&
                       $headers['Authorization'] === 'Bearer oauth-token-xyz';
            }));
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/api/transmissions', ['document' => '<Invoice/>'])
            ->andReturn(['transmission_id' => 'trans-456']);

        /* Act */
        $response = $this->client->request('POST', '/api/transmissions', ['document' => '<Invoice/>']);

        /* Assert */
        $this->assertEquals(['transmission_id' => 'trans-456'], $response);
    }

    #[Test]
    public function it_reuses_valid_access_token_for_subsequent_requests(): void
    {
        /* Arrange */
        config([
            'peppol.providers.peppyrus.client_id' => 'client-id-123',
            'peppol.providers.peppyrus.client_secret' => 'secret-abc',
        ]);
        
        // Mock OAuth token request (should be called only once)
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/oauth/token', Mockery::any())
            ->andReturn([
                'access_token' => 'oauth-token-xyz',
                'expires_in' => 3600,
            ]);
        
        // Mock setHeaders for both requests
        $this->mockApiClient->shouldReceive('setHeaders')->twice();
        
        // Mock two actual requests
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/api/transmissions', Mockery::any())
            ->andReturn(['transmission_id' => 'trans-1']);
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('GET', '/api/transmissions/trans-1/status', [])
            ->andReturn(['status' => 'sent']);

        /* Act */
        $this->client->request('POST', '/api/transmissions', ['document' => '<Invoice/>']);
        $this->client->request('GET', '/api/transmissions/trans-1/status');

        /* Assert - handled by shouldReceive */
    }

    #[Test]
    public function it_makes_post_request_with_oauth_token(): void
    {
        /* Arrange */
        config([
            'peppol.providers.peppyrus.client_id' => 'client-id-123',
            'peppol.providers.peppyrus.client_secret' => 'secret-abc',
        ]);
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/oauth/token', Mockery::any())
            ->andReturn(['access_token' => 'token-123', 'expires_in' => 3600]);
        
        $this->mockApiClient->shouldReceive('setHeaders')->once();
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/api/transmissions', ['data' => 'test'])
            ->andReturn(['id' => 999]);

        /* Act */
        $response = $this->client->request('POST', '/api/transmissions', ['data' => 'test']);

        /* Assert */
        $this->assertEquals(['id' => 999], $response);
    }

    #[Test]
    public function it_makes_get_request_with_oauth_token(): void
    {
        /* Arrange */
        config([
            'peppol.providers.peppyrus.client_id' => 'client-id-123',
            'peppol.providers.peppyrus.client_secret' => 'secret-abc',
        ]);
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/oauth/token', Mockery::any())
            ->andReturn(['access_token' => 'token-456', 'expires_in' => 3600]);
        
        $this->mockApiClient->shouldReceive('setHeaders')->once();
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('GET', '/api/transmissions/trans-123/status', [])
            ->andReturn(['status' => 'delivered']);

        /* Act */
        $response = $this->client->request('GET', '/api/transmissions/trans-123/status');

        /* Assert */
        $this->assertEquals(['status' => 'delivered'], $response);
    }

    #[Test]
    public function it_handles_token_without_expires_in(): void
    {
        /* Arrange */
        config([
            'peppol.providers.peppyrus.client_id' => 'client-id-123',
            'peppol.providers.peppyrus.client_secret' => 'secret-abc',
        ]);
        
        // Token response without expires_in should default to 3600 seconds
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/oauth/token', Mockery::any())
            ->andReturn(['access_token' => 'token-789']);
        
        $this->mockApiClient->shouldReceive('setHeaders')->once();
        
        $this->mockApiClient->shouldReceive('request')
            ->once()
            ->with('POST', '/api/transmissions', [])
            ->andReturn(['id' => 111]);

        /* Act */
        $response = $this->client->request('POST', '/api/transmissions');

        /* Assert */
        $this->assertEquals(['id' => 111], $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
