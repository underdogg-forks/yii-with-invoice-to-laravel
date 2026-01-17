<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\AcknowledgmentEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakePeppyrusClient;
use Tests\PeppolTestCase;

#[CoversClass(AcknowledgmentEndpoint::class)]
class AcknowledgmentEndpointTest extends PeppolTestCase
{
    private FakePeppyrusClient $fakeClient;
    private AcknowledgmentEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakePeppyrusClient();
        $this->endpoint = new AcknowledgmentEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_gets_acknowledgment(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'acknowledgment.acknowledged');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getAcknowledgment($fixture['transmission_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($response['acknowledged']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/transmissions/{$fixture['transmission_id']}/acknowledgment"));
    }

    #[Test]
    public function it_gets_acknowledgment_details(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'acknowledgment_details.received');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getAcknowledgmentDetails($fixture['acknowledgment_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/acknowledgments/{$fixture['acknowledgment_id']}"));
    }

    #[Test]
    public function it_handles_pending_acknowledgment(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'acknowledgment.pending');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getAcknowledgment($fixture['transmission_id']);

        /* Assert */
        $this->assertFalse($response['acknowledged']);
        $this->assertEquals('pending', $response['status']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/transmissions/{$fixture['transmission_id']}/acknowledgment"));
    }

    #[Test]
    public function it_handles_negative_acknowledgment(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'acknowledgment_details.rejected');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getAcknowledgmentDetails($fixture['acknowledgment_id']);

        /* Assert */
        $this->assertEquals('rejected', $response['status']);
        $this->assertNotEmpty($response['reason']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/acknowledgments/{$fixture['acknowledgment_id']}"));
    }
}
