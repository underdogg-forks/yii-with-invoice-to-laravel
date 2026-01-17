<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\WebhooksEndpoint;
use App\Services\Peppol\StoreCoveClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(WebhooksEndpoint::class)]
class WebhooksEndpointTest extends TestCase
{
    private StoreCoveClient $mockClient;
    private WebhooksEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(StoreCoveClient::class);
        $this->endpoint = new WebhooksEndpoint($this->mockClient);
    }

    #[Test]
    public function it_creates_webhook(): void
    {
        /* Arrange */
        $webhookData = [
            'url' => 'https://example.com/webhook',
            'events' => ['document.submitted', 'document.delivered'],
        ];
        
        $expectedResponse = ['id' => 'webhook-123', 'url' => 'https://example.com/webhook'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/webhooks', $webhookData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->createWebhook($webhookData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_webhook(): void
    {
        /* Arrange */
        $webhookId = 'webhook-456';
        $expectedResponse = [
            'id' => $webhookId,
            'url' => 'https://example.com/webhook',
            'events' => ['document.delivered'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/webhooks/{$webhookId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getWebhook($webhookId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_deletes_webhook(): void
    {
        /* Arrange */
        $webhookId = 'webhook-789';
        $expectedResponse = ['deleted' => true];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::DELETE->value, "/api/v2/webhooks/{$webhookId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->deleteWebhook($webhookId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_tests_webhook(): void
    {
        /* Arrange */
        $webhookId = 'webhook-test';
        $expectedResponse = [
            'test_sent' => true,
            'response_code' => 200,
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, "/api/v2/webhooks/{$webhookId}/test")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->testWebhook($webhookId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_creates_webhook_with_secret(): void
    {
        /* Arrange */
        $webhookData = [
            'url' => 'https://example.com/webhook',
            'events' => ['document.submitted'],
            'secret' => 'webhook-secret-key',
        ];
        
        $expectedResponse = ['id' => 'webhook-secure', 'url' => 'https://example.com/webhook'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/webhooks', $webhookData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->createWebhook($webhookData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
