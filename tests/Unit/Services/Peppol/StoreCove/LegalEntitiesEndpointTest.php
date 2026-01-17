<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\LegalEntitiesEndpoint;
use App\Services\Peppol\StoreCoveClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(LegalEntitiesEndpoint::class)]
class LegalEntitiesEndpointTest extends TestCase
{
    private StoreCoveClient $mockClient;
    private LegalEntitiesEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(StoreCoveClient::class);
        $this->endpoint = new LegalEntitiesEndpoint($this->mockClient);
    }

    #[Test]
    public function it_creates_legal_entity(): void
    {
        /* Arrange */
        $entityData = [
            'party_name' => 'Test Company BV',
            'identifiers' => [
                ['scheme' => '0088', 'id' => '1234567890123']
            ],
        ];
        
        $expectedResponse = ['id' => 123, 'party_name' => 'Test Company BV'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v2/legal_entities', $entityData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->createLegalEntity($entityData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_legal_entity(): void
    {
        /* Arrange */
        $entityId = '456';
        $expectedResponse = [
            'id' => 456,
            'party_name' => 'Example Corp',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v2/legal_entities/{$entityId}")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getLegalEntity($entityId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_updates_legal_entity(): void
    {
        /* Arrange */
        $entityId = '789';
        $entityData = ['party_name' => 'Updated Company Name'];
        $expectedResponse = ['id' => 789, 'party_name' => 'Updated Company Name'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::PUT->value, "/api/v2/legal_entities/{$entityId}", $entityData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->updateLegalEntity($entityId, $entityData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_lists_legal_entities_without_filters(): void
    {
        /* Arrange */
        $expectedResponse = [
            ['id' => 1, 'party_name' => 'Company 1'],
            ['id' => 2, 'party_name' => 'Company 2'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, '/api/v2/legal_entities')
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->listLegalEntities();

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_lists_legal_entities_with_filters(): void
    {
        /* Arrange */
        $filters = ['page' => 2, 'per_page' => 10];
        $expectedResponse = [
            ['id' => 11, 'party_name' => 'Company 11'],
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, '/api/v2/legal_entities?page=2&per_page=10')
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->listLegalEntities($filters);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
