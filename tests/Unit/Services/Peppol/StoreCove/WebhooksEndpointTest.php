<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\WebhooksEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeStoreCoveClient;
use Tests\PeppolTestCase;

#[CoversClass(WebhooksEndpoint::class)]
class WebhooksEndpointTest extends PeppolTestCase
{
    private FakeStoreCoveClient $fakeClient;
    private WebhooksEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeStoreCoveClient();
        $this->endpoint = new WebhooksEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_creates_webhook(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'webhooks.create');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->createWebhook($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/webhooks'));
    }

    #[Test]
    public function it_gets_webhook(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'webhooks.get');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getWebhook($fixture['webhook_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/webhooks/{$fixture['webhook_id']}"));
    }

    #[Test]
    public function it_deletes_webhook(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'webhooks.delete');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->deleteWebhook($fixture['webhook_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::DELETE->value, "/api/v2/webhooks/{$fixture['webhook_id']}"));
    }

    #[Test]
    public function it_tests_webhook(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'webhooks.test');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->testWebhook($fixture['webhook_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, "/api/v2/webhooks/{$fixture['webhook_id']}/test"));
    }

    #[Test]
    public function it_creates_webhook_with_secret(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'webhooks.create_with_secret');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->createWebhook($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/webhooks'));
    }
}
