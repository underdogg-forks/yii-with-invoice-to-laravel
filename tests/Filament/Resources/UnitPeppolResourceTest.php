<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\UnitPeppolResource;
use App\Filament\Resources\UnitPeppolResource\Pages\ListUnitPeppols;
use App\Models\Unit;
use App\Models\UnitPeppol;
use Filament\Actions\DeleteAction;
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
            ->test(UnitPeppolResource\Pages\CreateUnitPeppol::class)
            ->fillForm($payload)
            ->call('create');

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
            ->test(UnitPeppolResource\Pages\EditUnitPeppol::class, [
                'record' => $unitPeppol->id,
            ])
            ->fillForm($payload)
            ->call('save');

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
            ->test(UnitPeppolResource\Pages\EditUnitPeppol::class, [
                'record' => $unitPeppol->id,
            ])
            ->callAction(DeleteAction::class);

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
            ->test(UnitPeppolResource\Pages\CreateUnitPeppol::class)
            ->fillForm($payload)
            ->call('create');

        /* Assert */
        $component
            ->assertHasFormErrors(['unit_id']);
    }
    #endregion
}
