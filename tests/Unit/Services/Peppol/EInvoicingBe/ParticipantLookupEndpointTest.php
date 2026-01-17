<?php

namespace Tests\Unit\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBe\ParticipantLookupEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeEInvoicingBeClient;
use Tests\PeppolTestCase;

#[CoversClass(ParticipantLookupEndpoint::class)]
class ParticipantLookupEndpointTest extends PeppolTestCase
{
    private FakeEInvoicingBeClient $fakeClient;
    private ParticipantLookupEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeEInvoicingBeClient();
        $this->endpoint = new ParticipantLookupEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_looks_up_participant(): void
    {
        /* Arrange */
        $participantId = $this->loadFixture('einvoicing_be', 'participant_lookup.registered.participant_id');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'participant_lookup.registered.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->lookupParticipant($participantId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($response['registered']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/participants/{$participantId}"));
    }

    #[Test]
    public function it_gets_belgian_endpoint(): void
    {
        /* Arrange */
        $vatNumber = $this->loadFixture('einvoicing_be', 'participant_lookup.belgian_endpoint.vat_number');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'participant_lookup.belgian_endpoint.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->getBelgianEndpoint($vatNumber);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/participants/belgian/{$vatNumber}/endpoint"));
    }

    #[Test]
    public function it_handles_unregistered_participant(): void
    {
        /* Arrange */
        $participantId = $this->loadFixture('einvoicing_be', 'participant_lookup.unregistered.participant_id');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'participant_lookup.unregistered.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->lookupParticipant($participantId);

        /* Assert */
        $this->assertFalse($response['registered']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/participants/{$participantId}"));
    }

    #[Test]
    public function it_gets_belgian_endpoint_with_capabilities(): void
    {
        /* Arrange */
        $vatNumber = $this->loadFixture('einvoicing_be', 'participant_lookup.belgian_endpoint_with_capabilities.vat_number');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'participant_lookup.belgian_endpoint_with_capabilities.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->getBelgianEndpoint($vatNumber);

        /* Assert */
        $this->assertTrue($response['supports_peppol']);
        $this->assertNotEmpty($response['capabilities']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/participants/belgian/{$vatNumber}/endpoint"));
    }

    #[Test]
    public function it_handles_endpoint_not_found(): void
    {
        /* Arrange */
        $vatNumber = $this->loadFixture('einvoicing_be', 'participant_lookup.endpoint_not_found.vat_number');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'participant_lookup.endpoint_not_found.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->getBelgianEndpoint($vatNumber);

        /* Assert */
        $this->assertFalse($response['supports_peppol']);
        $this->assertNotEmpty($response['error']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/participants/belgian/{$vatNumber}/endpoint"));
    }
}
