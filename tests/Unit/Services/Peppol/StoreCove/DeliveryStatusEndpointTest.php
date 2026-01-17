<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\DeliveryStatusEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeStoreCoveClient;
use Tests\PeppolTestCase;

#[CoversClass(DeliveryStatusEndpoint::class)]
class DeliveryStatusEndpointTest extends PeppolTestCase
{
    private FakeStoreCoveClient $fakeClient;
    private DeliveryStatusEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeStoreCoveClient();
        $this->endpoint = new DeliveryStatusEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_gets_delivery_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'delivery_status.delivered');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($fixture['document_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/document_submissions/{$fixture['document_id']}/delivery_status"));
    }

    #[Test]
    public function it_gets_delivery_history(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'delivery_history');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryHistory($fixture['document_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/document_submissions/{$fixture['document_id']}/delivery_history"));
    }

    #[Test]
    public function it_checks_recipient_acknowledgment(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'recipient_acknowledgment.acknowledged');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->checkRecipientAcknowledgment($fixture['document_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/document_submissions/{$fixture['document_id']}/acknowledgment"));
    }

    #[Test]
    public function it_handles_pending_delivery_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'delivery_status.pending');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($fixture['document_id']);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/document_submissions/{$fixture['document_id']}/delivery_status"));
    }

    #[Test]
    public function it_handles_failed_delivery_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'delivery_status.failed');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDeliveryStatus($fixture['document_id']);

        /* Assert */
        $this->assertEquals('failed', $response['status']);
        $this->assertEquals('Recipient not found', $response['error']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/document_submissions/{$fixture['document_id']}/delivery_status"));
    }
}
