<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\QuoteDTO;
use App\Models\Quote;
use App\Models\Client;
use App\Models\User;
use App\Models\QuoteStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteDTOTest extends TestCase
{
    use RefreshDatabase;

    public function it_creates_quote_dto_from_array(): void
    {
        // Arrange
        $data = [
            'client_id' => 1,
            'quote_number' => 'Q-2026-001',
            'quote_date' => '2026-01-13',
            'expiry_date' => '2026-02-13',
            'subtotal' => 1000.00,
            'tax_total' => 210.00,
            'discount_total' => 50.00,
            'total' => 1160.00,
        ];

        // Act
        $dto = new QuoteDTO(
            client_id: $data['client_id'],
            quote_number: $data['quote_number'],
            quote_date: $data['quote_date'],
            expiry_date: $data['expiry_date'],
            subtotal: $data['subtotal'],
            tax_total: $data['tax_total'],
            discount_total: $data['discount_total'],
            total: $data['total']
        );

        // Assert
        $this->assertEquals($data['client_id'], $dto->client_id);
        $this->assertEquals($data['quote_number'], $dto->quote_number);
        $this->assertEquals($data['total'], $dto->total);
    }

    public function it_creates_dto_from_model(): void
    {
        // Arrange
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $status = QuoteStatus::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'quote_status_id' => $status->id,
        ]);

        // Act
        $dto = QuoteDTO::fromModel($quote);

        // Assert
        $this->assertEquals($quote->id, $dto->id);
        $this->assertEquals($quote->client_id, $dto->client_id);
        $this->assertEquals($quote->quote_number, $dto->quote_number);
        $this->assertEquals($quote->total, $dto->total);
    }

    public function it_converts_dto_to_array(): void
    {
        // Arrange
        $dto = new QuoteDTO(
            client_id: 1,
            quote_number: 'Q-2026-001',
            quote_date: '2026-01-13',
            expiry_date: '2026-02-13',
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
        $this->assertArrayHasKey('quote_number', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertEquals(1, $array['client_id']);
    }

    public function it_handles_optional_fields(): void
    {
        // Arrange & Act
        $dto = new QuoteDTO(
            client_id: 1,
            quote_number: 'Q-2026-001',
            quote_date: '2026-01-13',
            expiry_date: '2026-02-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 0,
            total: 1210.00,
            reference: 'TEST-REF',
            notes: 'Test notes'
        );

        // Assert
        $this->assertEquals('TEST-REF', $dto->reference);
        $this->assertEquals('Test notes', $dto->notes);
    }

    public function it_handles_null_optional_fields(): void
    {
        // Arrange & Act
        $dto = new QuoteDTO(
            client_id: 1,
            quote_number: 'Q-2026-001',
            quote_date: '2026-01-13',
            expiry_date: '2026-02-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 0,
            total: 1210.00
        );

        // Assert
        $this->assertNull($dto->reference);
        $this->assertNull($dto->notes);
    }

    public function it_includes_all_amount_fields(): void
    {
        // Arrange
        $dto = new QuoteDTO(
            client_id: 1,
            quote_number: 'Q-2026-001',
            quote_date: '2026-01-13',
            expiry_date: '2026-02-13',
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
