<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\PaymentPeppolResource;
use App\Filament\Resources\PaymentPeppolResource\Pages\ListPaymentPeppols;
use App\Models\Invoice;
use App\Models\PaymentPeppol;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(PaymentPeppolResource::class)]
class PaymentPeppolResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_payment_peppols(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        $payload = [
            'inv_id' => $invoice->id,
            'auto_reference' => 123456,
            'provider' => 'StoreCove',
        ];
        $paymentPeppol = PaymentPeppol::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListPaymentPeppols::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$paymentPeppol]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_payment_peppol(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        $payload = [
            'inv_id' => $invoice->id,
            'auto_reference' => 789012,
            'provider' => 'Ecosio',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListPaymentPeppols::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('payment_peppol', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_payment_peppol(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        $paymentPeppol = PaymentPeppol::factory()->create([
            'inv_id' => $invoice->id,
            'provider' => 'StoreCove',
        ]);

        $payload = [
            'provider' => 'Peppol',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListPaymentPeppols::class)
            ->mountAction(TestAction::make('edit')->table($paymentPeppol))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('payment_peppol', array_merge($payload, [
            'id' => $paymentPeppol->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_payment_peppol(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();
        $paymentPeppol = PaymentPeppol::factory()->create([
            'inv_id' => $invoice->id,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListPaymentPeppols::class)
            ->mountAction(TestAction::make('delete')->table($paymentPeppol))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('payment_peppol', [
            'id' => $paymentPeppol->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing inv_id
            'provider' => 'StoreCove',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListPaymentPeppols::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['inv_id']);
    }
    #endregion
}
