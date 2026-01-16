<?php

namespace Tests\Feature;

use App\Models\Unit;
use App\Models\UnitPeppol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(UnitPeppol::class)]
class UnitPeppolTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_unit_peppol(): void
    {
        /* Arrange */
        $unit = Unit::factory()->create();
        
        $payload = [
            'unit_id' => $unit->id,
            'code' => 'PCE',
            'name' => 'Piece',
            'description' => 'A single item',
        ];

        /* Act */
        $unitPeppol = UnitPeppol::factory()->create($payload);

        /* Assert */
        $this->assertDatabaseHas('unit_peppol', $payload);
        $this->assertNotNull($unitPeppol->id);
    }

    #[Test]
    public function it_has_unit_relationship(): void
    {
        /* Arrange */
        $unitPeppol = UnitPeppol::factory()->create();

        /* Act */
        $unit = $unitPeppol->unit;

        /* Assert */
        $this->assertInstanceOf(Unit::class, $unit);
    }

    #[Test]
    public function it_updates_unit_peppol(): void
    {
        /* Arrange */
        $unitPeppol = UnitPeppol::factory()->create([
            'code' => 'PCE',
            'name' => 'Piece',
        ]);

        $payload = [
            'code' => 'BOX',
            'name' => 'Box',
        ];

        /* Act */
        $unitPeppol->update($payload);

        /* Assert */
        $this->assertDatabaseHas('unit_peppol', [
            'id' => $unitPeppol->id,
            'code' => 'BOX',
            'name' => 'Box',
        ]);
    }

    #[Test]
    public function it_deletes_unit_peppol(): void
    {
        /* Arrange */
        $unitPeppol = UnitPeppol::factory()->create();
        $id = $unitPeppol->id;

        /* Act */
        $unitPeppol->delete();

        /* Assert */
        $this->assertDatabaseMissing('unit_peppol', [
            'id' => $id,
        ]);
    }
}
