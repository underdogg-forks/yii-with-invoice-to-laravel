<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\DeliveryEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeLetsPeppolClient;
use Tests\PeppolTestCase;

#[CoversClass(DeliveryEndpoint::class)]
class DeliveryEndpointTest extends PeppolTestCase
{
    private FakeLetsPeppolClient $fakeClient;
    private DeliveryEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeLetsPeppolClient();
        $this->endpoint = new DeliveryEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_gets_delivery_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'delivery_status.delivered');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($fixture['invoice_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/deliveries/{$fixture['invoice_id']}/status"));
    }

    #[Test]
    public function it_gets_delivery_report(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'delivery_report');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryReport($fixture['invoice_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertCount(2, $response['events']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/deliveries/{$fixture['invoice_id']}/report"));
    }

    #[Test]
    public function it_handles_pending_delivery_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'delivery_status.pending');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($fixture['invoice_id']);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/deliveries/{$fixture['invoice_id']}/status"));
    }

    #[Test]
    public function it_handles_failed_delivery_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'delivery_status.failed');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($fixture['invoice_id']);

        /* Assert */
        $this->assertEquals('failed', $response['status']);
        $this->assertNotEmpty($response['error']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/deliveries/{$fixture['invoice_id']}/status"));
    }
}
