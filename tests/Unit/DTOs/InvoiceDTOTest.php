<?php

namespace Tests\Unit\DTOs;

use Tests\TestCase;
use App\DTOs\InvoiceDTO;

class InvoiceDTOTest extends TestCase
{
    public function it_creates_dto_with_required_fields(): void
    {
        // Arrange & Act
        $dto = new InvoiceDTO(
            client_id: 1,
            date_invoice: '2026-01-01',
            date_due: '2026-01-31'
        );

        // Assert
        $this->assertEquals(1, $dto->client_id);
        $this->assertEquals('2026-01-01', $dto->date_invoice);
        $this->assertEquals('2026-01-31', $dto->date_due);
    }

    public function it_creates_dto_with_all_fields(): void
    {
        // Arrange & Act
        $dto = new InvoiceDTO(
            id: 1,
            client_id: 1,
            numbering_id: 1,
            status_id: 1,
            number: 'INV-0001',
            date_invoice: '2026-01-01',
            date_due: '2026-01-31',
            discount_percent: 10.0,
            discount_amount: 50.0,
            terms: 'Net 30'
        );

        // Assert
        $this->assertEquals(1, $dto->id);
        $this->assertEquals('INV-0001', $dto->number);
        $this->assertEquals(10.0, $dto->discount_percent);
        $this->assertEquals('Net 30', $dto->terms);
    }

    public function it_converts_to_array(): void
    {
        // Arrange
        $dto = new InvoiceDTO(
            id: 1,
            client_id: 1,
            date_invoice: '2026-01-01',
            date_due: '2026-01-31'
        );

        // Act
        $array = $dto->toArray();

        // Assert
        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('client_id', $array);
        $this->assertArrayHasKey('date_invoice', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals(1, $array['client_id']);
    }

    public function it_creates_dto_from_model(): void
    {
        // Arrange
        $invoice = new \App\Models\Invoice();
        $invoice->id = 1;
        $invoice->client_id = 1;
        $invoice->number = 'INV-0001';
        $invoice->date_invoice = '2026-01-01';
        $invoice->date_due = '2026-01-31';

        // Act
        $dto = InvoiceDTO::fromModel($invoice);

        // Assert
        $this->assertInstanceOf(InvoiceDTO::class, $dto);
        $this->assertEquals(1, $dto->id);
        $this->assertEquals('INV-0001', $dto->number);
    }
}
