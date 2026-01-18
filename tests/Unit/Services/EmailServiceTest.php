<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EmailService;
use App\Services\PdfService;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Mockery;

class EmailServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmailService $emailService;
    private $pdfServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->pdfServiceMock = Mockery::mock(PdfService::class);
        $this->emailService = new EmailService($this->pdfServiceMock);
        
        Mail::fake();
    }

    #[Test]
    public function it_sends_invoice_email_with_attached_pdf(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();
        $this->pdfServiceMock->shouldReceive('generateInvoicePdf')
            ->once()
            ->with($invoice)
            ->andReturn('pdf-content');

        /* Act */
        $result = $this->emailService->sendInvoice($invoice);

        /* Assert */
        $this->assertTrue($result);
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    #[Test]
    public function it_sends_quote_email_with_attached_pdf(): void
    {
        /* Arrange */
        $quote = Quote::factory()->create();
        $this->pdfServiceMock->shouldReceive('generateQuotePdf')
            ->once()
            ->with($quote)
            ->andReturn('pdf-content');

        /* Act */
        $result = $this->emailService->sendQuote($quote);

        /* Assert */
        $this->assertTrue($result);
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    #[Test]
    public function it_sends_sales_order_email_with_attached_pdf(): void
    {
        /* Arrange */
        $salesOrder = SalesOrder::factory()->create();
        $this->pdfServiceMock->shouldReceive('generateSalesOrderPdf')
            ->once()
            ->with($salesOrder)
            ->andReturn('pdf-content');

        /* Act */
        $result = $this->emailService->sendSalesOrder($salesOrder);

        /* Assert */
        $this->assertTrue($result);
        Mail::assertSent(\Illuminate\Mail\Mailable::class);
    }

    #[Test]
    public function it_allows_custom_recipient_email_address(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();
        $customEmail = 'custom@example.com';
        $this->pdfServiceMock->shouldReceive('generateInvoicePdf')
            ->once()
            ->andReturn('pdf-content');

        /* Act */
        $result = $this->emailService->sendInvoice($invoice, ['to' => $customEmail]);

        /* Assert */
        $this->assertTrue($result);
        Mail::assertSent(function ($mailable) use ($customEmail) {
            return $mailable->hasTo($customEmail);
        });
    }

    #[Test]
    public function it_allows_custom_email_subject(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();
        $customSubject = 'Custom Subject';
        $this->pdfServiceMock->shouldReceive('generateInvoicePdf')
            ->once()
            ->andReturn('pdf-content');

        /* Act */
        $result = $this->emailService->sendInvoice($invoice, ['subject' => $customSubject]);

        /* Assert */
        $this->assertTrue($result);
        Mail::assertSent(function ($mailable) use ($customSubject) {
            return $mailable->subject === $customSubject;
        });
    }

    #[Test]
    public function it_handles_email_send_failure_gracefully(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();
        $this->pdfServiceMock->shouldReceive('generateInvoicePdf')
            ->once()
            ->andThrow(new \Exception('PDF generation failed'));

        /* Act */
        $result = $this->emailService->sendInvoice($invoice);

        /* Assert */
        $this->assertFalse($result, 'Email service should return false when PDF generation fails');
        Mail::assertNothingSent();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
