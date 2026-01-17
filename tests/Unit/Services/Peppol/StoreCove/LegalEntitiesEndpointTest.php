<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\LegalEntitiesEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeStoreCoveClient;
use Tests\PeppolTestCase;

#[CoversClass(LegalEntitiesEndpoint::class)]
class LegalEntitiesEndpointTest extends PeppolTestCase
{
    private FakeStoreCoveClient $fakeClient;
    private LegalEntitiesEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeStoreCoveClient();
        $this->endpoint = new LegalEntitiesEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_creates_legal_entity(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'legal_entities.create');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->createLegalEntity($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/legal_entities'));
    }

    #[Test]
    public function it_gets_legal_entity(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'legal_entities.get');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getLegalEntity($fixture['entity_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/legal_entities/{$fixture['entity_id']}"));
    }

    #[Test]
    public function it_updates_legal_entity(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'legal_entities.update');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->updateLegalEntity($fixture['entity_id'], $fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::PUT->value, "/api/v2/legal_entities/{$fixture['entity_id']}"));
    }

    #[Test]
    public function it_lists_legal_entities_without_filters(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'legal_entities.list_without_filters');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->listLegalEntities();

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, '/api/v2/legal_entities'));
    }

    #[Test]
    public function it_lists_legal_entities_with_filters(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'legal_entities.list_with_filters');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->listLegalEntities($fixture['filters']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, '/api/v2/legal_entities?page=2&per_page=10'));
    }
}
