<?php

namespace Tests\Filament\Resources;

use App\Enums\NumberingEntityTypeEnum;
use App\Filament\Resources\InvoiceNumberingResource;
use App\Filament\Resources\InvoiceNumberingResource\Pages\ListInvoiceNumberings;
use App\Models\InvoiceNumbering;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(InvoiceNumberingResource::class)]
class InvoiceNumberingResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_invoice_numberings(): void
    {
        /* Arrange */
        $payload = [
            'name' => '::test_numbering::',
            'entity_type' => NumberingEntityTypeEnum::INVOICE->value,
            'identifier_format' => 'INV-{YEAR}-{NUMBER}',
            'next_id' => 1,
            'left_pad' => 4,
            'is_default' => false,
        ];
        $numbering = InvoiceNumbering::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListInvoiceNumberings::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$numbering]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_an_invoice_numbering(): void
    {
        /* Arrange */
        $payload = [
            'name' => 'New Numbering Scheme',
            'entity_type' => NumberingEntityTypeEnum::INVOICE->value,
            'identifier_format' => 'NEW-{NUMBER}',
            'next_id' => 1000,
            'left_pad' => 5,
            'is_default' => true,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListInvoiceNumberings::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('invoice_numbering', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_an_invoice_numbering(): void
    {
        /* Arrange */
        $numbering = InvoiceNumbering::factory()->create([
            'name' => 'Old Numbering',
            'identifier_format' => 'OLD-{NUMBER}',
            'next_id' => 1,
        ]);

        $payload = [
            'name' => 'Updated Numbering',
            'identifier_format' => 'UPD-{NUMBER}',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListInvoiceNumberings::class)
            ->mountAction(TestAction::make('edit')->table($numbering))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('invoice_numbering', array_merge($payload, [
            'id' => $numbering->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_an_invoice_numbering(): void
    {
        /* Arrange */
        $numbering = InvoiceNumbering::factory()->create();

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListInvoiceNumberings::class)
            ->mountAction(TestAction::make('delete')->table($numbering))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('invoice_numbering', [
            'id' => $numbering->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing name, entity_type, next_id, and left_pad
            'identifier_format' => 'TEST-{NUMBER}',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListInvoiceNumberings::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['name', 'entity_type', 'next_id', 'left_pad']);
    }
    #endregion
}
