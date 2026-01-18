<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoiceNumbering;
use App\Enums\InvoiceStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_client(): void
    {
        /* Arrange */
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);

        /* Act */
        $result = $invoice->client;

        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($client->id, $result->id);
    }

    #[Test]
    public function it_has_status_as_enum(): void
    {
        /* Arrange & Act */
        $invoice = Invoice::factory()->create(['status' => InvoiceStatusEnum::PAID]);

        /* Assert */
        $this->assertInstanceOf(InvoiceStatusEnum::class, $invoice->status);
        $this->assertEquals(InvoiceStatusEnum::PAID, $invoice->status);
    }

    #[Test]
    public function it_belongs_to_numbering(): void
    {
        /* Arrange */
        $numbering = InvoiceNumbering::factory()->create();
        $invoice = Invoice::factory()->create(['numbering_id' => $numbering->id]);

        /* Act */
        $result = $invoice->numbering;

        /* Assert */
        $this->assertInstanceOf(InvoiceNumbering::class, $result);
        $this->assertEquals($numbering->id, $result->id);
    }

    #[Test]
    public function it_has_many_items(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();
        $items = \App\Models\InvoiceItem::factory()->count(3)->create([
            'invoice_id' => $invoice->id
        ]);

        /* Act */
        $result = $invoice->items;

        /* Assert */
        $this->assertCount(3, $result);
        $this->assertEquals($items->pluck('id')->sort()->values(), $result->pluck('id')->sort()->values());
    }

    #[Test]
    public function it_detects_overdue_invoices(): void
    {
        /* Arrange */
        $overdueInvoice = Invoice::factory()->create([
            'date_due' => now()->subDays(5)->format('Y-m-d'),
        ]);
        $currentInvoice = Invoice::factory()->create([
            'date_due' => now()->addDays(5)->format('Y-m-d'),
        ]);

        /* Act */
        $overdueInvoices = Invoice::overdue()->get();

        /* Assert */
        $this->assertTrue($overdueInvoices->contains($overdueInvoice));
        $this->assertFalse($overdueInvoices->contains($currentInvoice));
    }
}
