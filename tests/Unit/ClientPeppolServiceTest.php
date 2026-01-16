<?php

namespace Tests\Unit;

use App\DTOs\ClientPeppolDTO;
use App\Models\ClientPeppol;
use App\Repositories\ClientPeppolRepository;
use App\Services\ClientPeppolService;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClientPeppolService::class)]
class ClientPeppolServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_gets_client_peppol_by_id(): void
    {
        /* Arrange */
        $repository = Mockery::mock(ClientPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(new ClientPeppol(['id' => 1]));

        $service = new ClientPeppolService($repository);

        /* Act */
        $result = $service->getById(1);

        /* Assert */
        $this->assertInstanceOf(ClientPeppol::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_creates_client_peppol(): void
    {
        /* Arrange */
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

        /* Act */
        $result = $service->create($dto);

        /* Assert */
        $this->assertInstanceOf(ClientPeppol::class, $result);
    }

    #[Test]
    public function it_deletes_client_peppol(): void
    {
        /* Arrange */
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

        /* Act */
        $result = $service->delete(1);

        /* Assert */
        $this->assertTrue($result);
    }
}
