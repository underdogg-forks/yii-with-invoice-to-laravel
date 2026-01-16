<?php

namespace Tests\Filament\Resources;

use App\Enums\InvoiceStatusEnum;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use App\Models\Client;
use App\Models\Invoice;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(InvoiceResource::class)]
class InvoiceResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_invoices(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'invoice_number' => 'INV-001',
            'invoice_date_created' => now(),
            'invoice_date_due' => now()->addDays(30),
            'invoice_status_id' => InvoiceStatusEnum::DRAFT->value,
        ];
        $invoice = Invoice::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListInvoices::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$invoice]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_an_invoice(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'invoice_number' => 'INV-NEW-001',
            'invoice_date_created' => now()->toDateString(),
            'invoice_date_due' => now()->addDays(30)->toDateString(),
            'invoice_status_id' => InvoiceStatusEnum::DRAFT->value,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(InvoiceResource\Pages\CreateInvoice::class)
            ->fillForm($payload)
            ->call('create');

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => $payload['invoice_number'],
            'client_id' => $payload['client_id'],
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_an_invoice(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => '::client_name::',
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-OLD-001',
            'invoice_status_id' => InvoiceStatusEnum::DRAFT->value,
        ]);

        $payload = [
            'invoice_number' => 'INV-UPDATED-001',
            'invoice_status_id' => InvoiceStatusEnum::SENT->value,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(InvoiceResource\Pages\EditInvoice::class, [
                'record' => $invoice->id,
            ])
            ->fillForm($payload)
            ->call('save');

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('invoices', array_merge($payload, [
            'id' => $invoice->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_an_invoice(): void
    {
        /* Arrange */
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(InvoiceResource\Pages\EditInvoice::class, [
                'record' => $invoice->id,
            ])
            ->callAction(DeleteAction::class);

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing client_id
            'invoice_number' => 'INV-TEST',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(InvoiceResource\Pages\CreateInvoice::class)
            ->fillForm($payload)
            ->call('create');

        /* Assert */
        $component
            ->assertHasFormErrors(['client_id']);
    }
    #endregion
}
