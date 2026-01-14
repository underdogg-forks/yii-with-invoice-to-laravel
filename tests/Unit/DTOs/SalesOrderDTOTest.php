<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\SalesOrderDTO;
use App\Models\SalesOrder;
use App\Models\Client;
use App\Models\User;
use App\Models\SalesOrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesOrderDTOTest extends TestCase
{
    use RefreshDatabase;

    public function it_creates_sales_order_dto_from_array(): void
    {
        // Arrange
        $data = [
            'client_id' => 1,
            'so_number' => 'SO-2026-001',
            'order_date' => '2026-01-13',
            'subtotal' => 1000.00,
            'tax_total' => 210.00,
            'discount_total' => 50.00,
            'total' => 1160.00,
        ];

        // Act
        $dto = new SalesOrderDTO(
            client_id: $data['client_id'],
            so_number: $data['so_number'],
            order_date: $data['order_date'],
            subtotal: $data['subtotal'],
            tax_total: $data['tax_total'],
            discount_total: $data['discount_total'],
            total: $data['total']
        );

        // Assert
        $this->assertEquals($data['client_id'], $dto->client_id);
        $this->assertEquals($data['so_number'], $dto->so_number);
        $this->assertEquals($data['total'], $dto->total);
    }

    public function it_creates_dto_from_model(): void
    {
        // Arrange
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $status = SalesOrderStatus::factory()->create();

        $salesOrder = SalesOrder::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'sales_order_status_id' => $status->id,
        ]);

        // Act
        $dto = SalesOrderDTO::fromModel($salesOrder);

        // Assert
        $this->assertEquals($salesOrder->id, $dto->id);
        $this->assertEquals($salesOrder->client_id, $dto->client_id);
        $this->assertEquals($salesOrder->so_number, $dto->so_number);
        $this->assertEquals($salesOrder->total, $dto->total);
    }

    public function it_converts_dto_to_array(): void
    {
        // Arrange
        $dto = new SalesOrderDTO(
            client_id: 1,
            so_number: 'SO-2026-001',
            order_date: '2026-01-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 50.00,
            total: 1160.00
        );

        // Act
        $array = $dto->toArray();

        // Assert
        $this->assertIsArray($array);
        $this->assertArrayHasKey('client_id', $array);
        $this->assertArrayHasKey('so_number', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertEquals(1, $array['client_id']);
    }

    public function it_handles_optional_fields(): void
    {
        // Arrange & Act
        $dto = new SalesOrderDTO(
            client_id: 1,
            so_number: 'SO-2026-001',
            order_date: '2026-01-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 0,
            total: 1210.00,
            reference: 'TEST-REF',
            delivery_date: '2026-02-13',
            notes: 'Test notes'
        );

        // Assert
        $this->assertEquals('TEST-REF', $dto->reference);
        $this->assertEquals('2026-02-13', $dto->delivery_date);
        $this->assertEquals('Test notes', $dto->notes);
    }

    public function it_handles_null_optional_fields(): void
    {
        // Arrange & Act
        $dto = new SalesOrderDTO(
            client_id: 1,
            so_number: 'SO-2026-001',
            order_date: '2026-01-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 0,
            total: 1210.00
        );

        // Assert
        $this->assertNull($dto->reference);
        $this->assertNull($dto->delivery_date);
        $this->assertNull($dto->notes);
    }

    public function it_includes_all_amount_fields(): void
    {
        // Arrange
        $dto = new SalesOrderDTO(
            client_id: 1,
            so_number: 'SO-2026-001',
            order_date: '2026-01-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 50.00,
            total: 1160.00
        );

        // Act
        $array = $dto->toArray();

        // Assert
        $this->assertEquals(1000.00, $array['subtotal']);
        $this->assertEquals(210.00, $array['tax_total']);
        $this->assertEquals(50.00, $array['discount_total']);
        $this->assertEquals(1160.00, $array['total']);
    }
}
