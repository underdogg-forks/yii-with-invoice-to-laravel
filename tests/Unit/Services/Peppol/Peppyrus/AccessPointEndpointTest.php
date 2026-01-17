<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\AccessPointEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakePeppyrusClient;
use Tests\PeppolTestCase;

#[CoversClass(AccessPointEndpoint::class)]
class AccessPointEndpointTest extends PeppolTestCase
{
    private FakePeppyrusClient $fakeClient;
    private AccessPointEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakePeppyrusClient();
        $this->endpoint = new AccessPointEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_queries_access_point(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'access_point_query.found');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->queryAccessPoint($fixture['participant_id'], $fixture['document_type']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, '/api/access-points/query'));
    }

    #[Test]
    public function it_gets_access_point_metadata(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'access_point_metadata');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getAccessPointMetadata($fixture['access_point_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/access-points/{$fixture['access_point_id']}/metadata"));
    }

    #[Test]
    public function it_queries_access_point_for_credit_note(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'access_point_query.credit_note');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->queryAccessPoint($fixture['participant_id'], $fixture['document_type']);

        /* Assert */
        $this->assertTrue($response['supports_document_type']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, '/api/access-points/query'));
    }

    #[Test]
    public function it_handles_access_point_not_found(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'access_point_query.not_found');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->queryAccessPoint($fixture['participant_id'], $fixture['document_type']);

        /* Assert */
        $this->assertFalse($response['found']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, '/api/access-points/query'));
    }
}
