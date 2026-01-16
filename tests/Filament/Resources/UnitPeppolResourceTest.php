<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\UnitPeppolResource;
use App\Filament\Resources\UnitPeppolResource\Pages\ListUnitPeppols;
use App\Models\Unit;
use App\Models\UnitPeppol;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(UnitPeppolResource::class)]
class UnitPeppolResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_unit_peppols(): void
    {
        /* Arrange */
        $unit = Unit::factory()->create();

        $payload = [
            'unit_id' => $unit->id,
            'code' => 'PCE',
            'name' => 'Piece',
            'description' => 'A single item',
        ];
        $unitPeppol = UnitPeppol::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListUnitPeppols::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$unitPeppol]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_unit_peppol(): void
    {
        /* Arrange */
        $unit = Unit::factory()->create();

        $payload = [
            'unit_id' => $unit->id,
            'code' => 'BOX',
            'name' => 'Box',
            'description' => 'A box container',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListUnitPeppols::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('unit_peppol', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_unit_peppol(): void
    {
        /* Arrange */
        $unit = Unit::factory()->create();

        $unitPeppol = UnitPeppol::factory()->create([
            'unit_id' => $unit->id,
            'code' => 'PCE',
            'name' => 'Piece',
        ]);

        $payload = [
            'code' => 'PKG',
            'name' => 'Package',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListUnitPeppols::class)
            ->mountAction(TestAction::make('edit')->table($unitPeppol))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('unit_peppol', array_merge($payload, [
            'id' => $unitPeppol->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_unit_peppol(): void
    {
        /* Arrange */
        $unit = Unit::factory()->create();
        $unitPeppol = UnitPeppol::factory()->create([
            'unit_id' => $unit->id,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListUnitPeppols::class)
            ->mountAction(TestAction::make('delete')->table($unitPeppol))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('unit_peppol', [
            'id' => $unitPeppol->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing unit_id
            'code' => 'PCE',
            'name' => 'Piece',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListUnitPeppols::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['unit_id']);
    }
    #endregion
}
