<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PdfService;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PdfServiceTest extends TestCase
{
    use RefreshDatabase;

    private PdfService $pdfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdfService = app(PdfService::class);
    }

    public function it_generates_invoice_pdf(): void
    {
        // Arrange
        $invoice = Invoice::factory()->create();

        // Act
        $pdf = $this->pdfService->generateInvoicePdf($invoice);

        // Assert
        $this->assertNotEmpty($pdf);
        $this->assertStringContainsString('%PDF', $pdf);
    }

    public function it_generates_quote_pdf(): void
    {
        // Arrange
        $quote = Quote::factory()->create();

        // Act
        $pdf = $this->pdfService->generateQuotePdf($quote);

        // Assert
        $this->assertNotEmpty($pdf);
        $this->assertStringContainsString('%PDF', $pdf);
    }

    public function it_generates_sales_order_pdf(): void
    {
        // Arrange
        $salesOrder = SalesOrder::factory()->create();

        // Act
        $pdf = $this->pdfService->generateSalesOrderPdf($salesOrder);

        // Assert
        $this->assertNotEmpty($pdf);
        $this->assertStringContainsString('%PDF', $pdf);
    }

    public function it_saves_invoice_pdf_to_storage(): void
    {
        // Arrange
        $invoice = Invoice::factory()->create();

        // Act
        $path = $this->pdfService->saveInvoicePdf($invoice);

        // Assert
        $this->assertNotEmpty($path);
        $this->assertFileExists(storage_path('app/' . $path));
    }

    public function it_uses_custom_template(): void
    {
        // Arrange
        $invoice = Invoice::factory()->create();
        $template = 'default';

        // Act
        $pdf = $this->pdfService->generateInvoicePdf($invoice, $template);

        // Assert
        $this->assertNotEmpty($pdf);
        $this->assertStringContainsString('%PDF', $pdf);
    }

    public function it_handles_missing_invoice(): void
    {
        // Arrange
        $this->expectException(\Exception::class);

        // Act
        $this->pdfService->generateInvoicePdf(null);
    }
}
