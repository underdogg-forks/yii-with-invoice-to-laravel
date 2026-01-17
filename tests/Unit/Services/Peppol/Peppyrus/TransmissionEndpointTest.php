<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\TransmissionEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakePeppyrusClient;
use Tests\PeppolTestCase;

#[CoversClass(TransmissionEndpoint::class)]
class TransmissionEndpointTest extends PeppolTestCase
{
    private FakePeppyrusClient $fakeClient;
    private TransmissionEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakePeppyrusClient();
        $this->endpoint = new TransmissionEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_transmits_document(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'transmission.basic');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->transmitDocument($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/transmissions'));
    }

    #[Test]
    public function it_gets_transmission_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'transmission_status.delivered');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getTransmissionStatus($fixture['transmission_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/transmissions/{$fixture['transmission_id']}/status"));
    }

    #[Test]
    public function it_retries_transmission(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'transmission_retry');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->retryTransmission($fixture['transmission_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, "/api/transmissions/{$fixture['transmission_id']}/retry"));
    }

    #[Test]
    public function it_transmits_document_with_metadata(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'transmission.with_metadata');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->transmitDocument($fixture['request']);

        /* Assert */
        $this->assertEquals('trans-meta', $response['transmission_id']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/transmissions'));
    }

    #[Test]
    public function it_handles_pending_transmission_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'transmission_status.pending');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getTransmissionStatus($fixture['transmission_id']);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/transmissions/{$fixture['transmission_id']}/status"));
    }

    #[Test]
    public function it_handles_failed_transmission_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'transmission_status.failed');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getTransmissionStatus($fixture['transmission_id']);

        /* Assert */
        $this->assertEquals('failed', $response['status']);
        $this->assertNotEmpty($response['error']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/transmissions/{$fixture['transmission_id']}/status"));
    }
}
