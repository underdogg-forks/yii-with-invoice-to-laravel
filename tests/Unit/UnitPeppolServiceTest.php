<?php

namespace Tests\Unit;

use App\DTOs\UnitPeppolDTO;
use App\Models\UnitPeppol;
use App\Repositories\UnitPeppolRepository;
use App\Services\UnitPeppolService;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnitPeppolService::class)]
class UnitPeppolServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_gets_unit_peppol_by_id(): void
    {
        /* Arrange */
        $repository = Mockery::mock(UnitPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(new UnitPeppol(['id' => 1]));

        $service = new UnitPeppolService($repository);

        /* Act */
        $result = $service->getById(1);

        /* Assert */
        $this->assertInstanceOf(UnitPeppol::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_gets_unit_peppol_by_unit_id(): void
    {
        /* Arrange */
        $repository = Mockery::mock(UnitPeppolRepository::class);
        $repository->shouldReceive('findByUnitId')
            ->once()
            ->with(1)
            ->andReturn(new UnitPeppol(['unit_id' => 1]));

        $service = new UnitPeppolService($repository);

        /* Act */
        $result = $service->getByUnitId(1);

        /* Assert */
        $this->assertInstanceOf(UnitPeppol::class, $result);
    }

    #[Test]
    public function it_creates_unit_peppol(): void
    {
        /* Arrange */
        $dto = new UnitPeppolDTO(
            unit_id: 1,
            code: 'PCE',
            name: 'Piece',
            description: 'A single item'
        );

        $repository = Mockery::mock(UnitPeppolRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($dto) {
                return is_array($arg) 
                    && $arg['code'] === $dto->code
                    && $arg['name'] === $dto->name
                    && $arg['description'] === $dto->description;
            }))
            ->andReturn(new UnitPeppol(['id' => 1]));

        $service = new UnitPeppolService($repository);

        /* Act */
        $result = $service->create($dto);

        /* Assert */
        $this->assertInstanceOf(UnitPeppol::class, $result);
    }

    #[Test]
    public function it_updates_unit_peppol(): void
    {
        /* Arrange */
        $unitPeppol = new UnitPeppol(['id' => 1]);
        $dto = new UnitPeppolDTO(
            id: 1,
            unit_id: 1,
            code: 'BOX',
            name: 'Box'
        );
        
        $repository = Mockery::mock(UnitPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($unitPeppol);
        $repository->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $service = new UnitPeppolService($repository);

        /* Act */
        $result = $service->update(1, $dto);

        /* Assert */
        $this->assertTrue($result);
    }

    #[Test]
    public function it_deletes_unit_peppol(): void
    {
        /* Arrange */
        $unitPeppol = new UnitPeppol(['id' => 1]);
        
        $repository = Mockery::mock(UnitPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($unitPeppol);
        $repository->shouldReceive('delete')
            ->once()
            ->with($unitPeppol)
            ->andReturn(true);

        $service = new UnitPeppolService($repository);

        /* Act */
        $result = $service->delete(1);

        /* Assert */
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_updating_non_existent_unit_peppol(): void
    {
        /* Arrange */
        $dto = new UnitPeppolDTO(
            id: 999,
            unit_id: 1,
            code: 'PCE',
            name: 'Piece'
        );
        
        $repository = Mockery::mock(UnitPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new UnitPeppolService($repository);

        /* Act */
        $result = $service->update(999, $dto);

        /* Assert */
        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_false_when_deleting_non_existent_unit_peppol(): void
    {
        /* Arrange */
        $repository = Mockery::mock(UnitPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new UnitPeppolService($repository);

        /* Act */
        $result = $service->delete(999);

        /* Assert */
        $this->assertFalse($result);
    }
}
