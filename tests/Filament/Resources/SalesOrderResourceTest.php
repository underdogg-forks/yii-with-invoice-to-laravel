<?php

namespace Tests\Filament\Resources;

use App\Enums\SalesOrderStatusEnum;
use App\Filament\Resources\SalesOrderResource;
use App\Filament\Resources\SalesOrderResource\Pages\ListSalesOrders;
use App\Models\Client;
use App\Models\SalesOrder;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(SalesOrderResource::class)]
class SalesOrderResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_sales_orders(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'client_name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'so_number' => 'SO-001',
            'order_date' => now()->format('Y-m-d'),
            'expected_delivery_date' => now()->addDays(14)->format('Y-m-d'),
            'status_id' => SalesOrderStatusEnum::PENDING->value,
            'subtotal' => 1000.00,
            'tax_total' => 210.00,
            'total_amount' => 1210.00,
        ];
        $salesOrder = SalesOrder::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListSalesOrders::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$salesOrder]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_sales_order(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'client_name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'so_number' => 'SO-NEW-001',
            'order_date' => now()->format('Y-m-d'),
            'expected_delivery_date' => now()->addDays(14)->format('Y-m-d'),
            'status_id' => SalesOrderStatusEnum::PENDING->value,
            'subtotal' => 500.00,
            'tax_total' => 105.00,
            'discount_amount' => 0,
            'discount_percent' => 0,
            'total_amount' => 605.00,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListSalesOrders::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('sales_orders', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_sales_order(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'client_name' => '::client_name::',
        ]);

        $salesOrder = SalesOrder::factory()->create([
            'client_id' => $client->id,
            'so_number' => 'SO-OLD-001',
            'order_date' => now()->format('Y-m-d'),
            'status_id' => SalesOrderStatusEnum::PENDING->value,
            'total_amount' => 1000.00,
        ]);

        $payload = [
            'so_number' => 'SO-UPDATED-001',
            'total_amount' => 1500.00,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListSalesOrders::class)
            ->mountAction(TestAction::make('edit')->table($salesOrder))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('sales_orders', array_merge($payload, [
            'id' => $salesOrder->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_sales_order(): void
    {
        /* Arrange */
        $client = Client::factory()->create();
        $salesOrder = SalesOrder::factory()->create([
            'client_id' => $client->id,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListSalesOrders::class)
            ->mountAction(TestAction::make('delete')->table($salesOrder))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('sales_orders', [
            'id' => $salesOrder->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing client_id, so_number, order_date
            'subtotal' => 100.00,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListSalesOrders::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['client_id', 'so_number', 'order_date']);
    }
    #endregion
}
