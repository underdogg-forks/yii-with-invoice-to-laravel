<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\ParticipantEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeLetsPeppolClient;
use Tests\PeppolTestCase;

#[CoversClass(ParticipantEndpoint::class)]
class ParticipantEndpointTest extends PeppolTestCase
{
    private FakeLetsPeppolClient $fakeClient;
    private ParticipantEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeLetsPeppolClient();
        $this->endpoint = new ParticipantEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_looks_up_participant(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'participant_lookup.registered');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->lookupParticipant($fixture['participant_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/participants/{$fixture['participant_id']}"));
    }

    #[Test]
    public function it_gets_participant_details(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'participant_details');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getParticipantDetails($fixture['participant_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/participants/{$fixture['participant_id']}/details"));
    }

    #[Test]
    public function it_validates_endpoint(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'endpoint_validation.valid_invoice');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateEndpoint($fixture['participant_id'], $fixture['document_type']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/participants/validate'));
    }

    #[Test]
    public function it_handles_unregistered_participant(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'participant_lookup.unregistered');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->lookupParticipant($fixture['participant_id']);

        /* Assert */
        $this->assertFalse($response['registered']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/participants/{$fixture['participant_id']}"));
    }

    #[Test]
    public function it_validates_endpoint_for_credit_note(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'endpoint_validation.valid_credit_note');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateEndpoint($fixture['participant_id'], $fixture['document_type']);

        /* Assert */
        $this->assertTrue($response['valid']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/participants/validate'));
    }
}
