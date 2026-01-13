<?php

namespace Tests\Unit;

use App\Services\ClientPeppolService;
use App\Repositories\ClientPeppolRepository;
use App\DTOs\ClientPeppolDTO;
use App\Models\ClientPeppol;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClientPeppolServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_get_by_id(): void
    {
        $repository = Mockery::mock(ClientPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(new ClientPeppol(['id' => 1]));

        $service = new ClientPeppolService($repository);
        $result = $service->getById(1);

        $this->assertInstanceOf(ClientPeppol::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function test_can_create(): void
    {
        $dto = new ClientPeppolDTO(
            client_id: 1,
            endpointid: 'test@example.com',
            buyer_reference: 'REF-001'
        );

        $repository = Mockery::mock(ClientPeppolRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->andReturn(new ClientPeppol(['id' => 1]));

        $service = new ClientPeppolService($repository);
        $result = $service->create($dto);

        $this->assertInstanceOf(ClientPeppol::class, $result);
    }

    public function test_can_delete(): void
    {
        $clientPeppol = new ClientPeppol(['id' => 1]);
        
        $repository = Mockery::mock(ClientPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($clientPeppol);
        $repository->shouldReceive('delete')
            ->once()
            ->with($clientPeppol)
            ->andReturn(true);

        $service = new ClientPeppolService($repository);
        $result = $service->delete(1);

        $this->assertTrue($result);
    }
}
