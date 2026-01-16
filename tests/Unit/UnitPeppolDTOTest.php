<?php

namespace Tests\Unit;

use App\DTOs\UnitPeppolDTO;
use App\Models\UnitPeppol;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnitPeppolDTO::class)]
class UnitPeppolDTOTest extends TestCase
{
    #[Test]
    public function it_creates_unit_peppol_dto(): void
    {
        /* Arrange */
        $expectedId = 1;
        $expectedUnitId = 1;
        $expectedCode = 'PCE';
        $expectedName = 'Piece';
        $expectedDescription = 'A single item';

        /* Act */
        $dto = new UnitPeppolDTO(
            id: $expectedId,
            unit_id: $expectedUnitId,
            code: $expectedCode,
            name: $expectedName,
            description: $expectedDescription
        );

        /* Assert */
        $this->assertEquals($expectedId, $dto->id);
        $this->assertEquals($expectedUnitId, $dto->unit_id);
        $this->assertEquals($expectedCode, $dto->code);
        $this->assertEquals($expectedName, $dto->name);
        $this->assertEquals($expectedDescription, $dto->description);
    }

    #[Test]
    public function it_converts_dto_to_array(): void
    {
        /* Arrange */
        $dto = new UnitPeppolDTO(
            id: 1,
            unit_id: 1,
            code: 'PCE',
            name: 'Piece',
            description: 'A single item'
        );

        /* Act */
        $array = $dto->toArray();

        /* Assert */
        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('unit_id', $array);
        $this->assertArrayHasKey('code', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('description', $array);
    }

    #[Test]
    public function it_creates_dto_from_model(): void
    {
        /* Arrange */
        $model = new UnitPeppol([
            'id' => 1,
            'unit_id' => 1,
            'code' => 'BOX',
            'name' => 'Box',
            'description' => 'A box container',
        ]);

        /* Act */
        $dto = UnitPeppolDTO::fromModel($model);

        /* Assert */
        $this->assertInstanceOf(UnitPeppolDTO::class, $dto);
        $this->assertEquals($model->id, $dto->id);
        $this->assertEquals($model->unit_id, $dto->unit_id);
        $this->assertEquals($model->code, $dto->code);
        $this->assertEquals($model->name, $dto->name);
        $this->assertEquals($model->description, $dto->description);
    }
}
