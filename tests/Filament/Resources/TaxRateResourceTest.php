<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\TaxRateResource;
use App\Filament\Resources\TaxRateResource\Pages\ListTaxRates;
use App\Models\TaxRate;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(TaxRateResource::class)]
class TaxRateResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_tax_rates(): void
    {
        /* Arrange */
        $payload = [
            'name' => 'Standard VAT',
            'rate' => 21.00,
            'is_default' => true,
        ];
        $taxRate = TaxRate::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTaxRates::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$taxRate]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_tax_rate(): void
    {
        /* Arrange */
        $payload = [
            'name' => 'Reduced VAT',
            'rate' => 9.00,
            'is_default' => false,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTaxRates::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tax_rates', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_tax_rate(): void
    {
        /* Arrange */
        $taxRate = TaxRate::factory()->create([
            'name' => 'Old Tax Rate',
            'rate' => 15.00,
            'is_default' => false,
        ]);

        $payload = [
            'name' => 'Updated Tax Rate',
            'rate' => 20.00,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTaxRates::class)
            ->mountAction(TestAction::make('edit')->table($taxRate))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tax_rates', array_merge($payload, [
            'id' => $taxRate->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_tax_rate(): void
    {
        /* Arrange */
        $taxRate = TaxRate::factory()->create([
            'name' => 'To Be Deleted',
            'rate' => 5.00,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTaxRates::class)
            ->mountAction(TestAction::make('delete')->table($taxRate))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('tax_rates', [
            'id' => $taxRate->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing name and rate
            'is_default' => false,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTaxRates::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['name', 'rate']);
    }
    #endregion
}
