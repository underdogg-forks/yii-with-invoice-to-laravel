<?php

namespace Tests\Filament\Resources;

use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource;
use App\Filament\Resources\QuoteResource\Pages\ListQuotes;
use App\Models\Client;
use App\Models\Quote;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(QuoteResource::class)]
class QuoteResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_quotes(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'client_name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'quote_number' => 'Q-001',
            'quote_date' => now()->format('Y-m-d'),
            'expiry_date' => now()->addDays(30)->format('Y-m-d'),
            'status_id' => QuoteStatusEnum::DRAFT->value,
            'subtotal' => 1000.00,
            'tax_total' => 210.00,
            'total_amount' => 1210.00,
        ];
        $quote = Quote::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListQuotes::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$quote]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_quote(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'client_name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'quote_number' => 'Q-NEW-001',
            'quote_date' => now()->format('Y-m-d'),
            'expiry_date' => now()->addDays(30)->format('Y-m-d'),
            'status_id' => QuoteStatusEnum::DRAFT->value,
            'subtotal' => 500.00,
            'tax_total' => 105.00,
            'discount_amount' => 0,
            'discount_percent' => 0,
            'total_amount' => 605.00,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListQuotes::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('quotes', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_quote(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'client_name' => '::client_name::',
        ]);

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'quote_number' => 'Q-OLD-001',
            'quote_date' => now()->format('Y-m-d'),
            'expiry_date' => now()->addDays(30)->format('Y-m-d'),
            'status_id' => QuoteStatusEnum::DRAFT->value,
            'total_amount' => 1000.00,
        ]);

        $payload = [
            'quote_number' => 'Q-UPDATED-001',
            'total_amount' => 1500.00,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListQuotes::class)
            ->mountAction(TestAction::make('edit')->table($quote))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('quotes', array_merge($payload, [
            'id' => $quote->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_quote(): void
    {
        /* Arrange */
        $client = Client::factory()->create();
        $quote = Quote::factory()->create([
            'client_id' => $client->id,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListQuotes::class)
            ->mountAction(TestAction::make('delete')->table($quote))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('quotes', [
            'id' => $quote->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing client_id, quote_number, quote_date, expiry_date
            'subtotal' => 100.00,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListQuotes::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['client_id', 'quote_number', 'quote_date', 'expiry_date']);
    }
    #endregion
}
