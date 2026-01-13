<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function __construct(
        private PdfService $pdfService
    ) {}

    /**
     * Send invoice email with PDF attachment
     */
    public function sendInvoice(Invoice $invoice, array $options = []): bool
    {
        try {
            $to = $options['to'] ?? $invoice->client->email;
            $subject = $options['subject'] ?? "Invoice {$invoice->invoice_number}";
            
            // Generate PDF
            $pdf = $this->pdfService->generateInvoicePdf($invoice);
            
            Mail::send('emails.invoice', ['invoice' => $invoice], function ($message) use ($to, $subject, $pdf, $invoice) {
                $message->to($to)
                    ->subject($subject)
                    ->attachData($pdf, "invoice-{$invoice->invoice_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });
            
            Log::info("Invoice email sent", ['invoice_id' => $invoice->id, 'to' => $to]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice email", [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send quote email with PDF attachment
     */
    public function sendQuote(Quote $quote, array $options = []): bool
    {
        try {
            $to = $options['to'] ?? $quote->client->email;
            $subject = $options['subject'] ?? "Quote {$quote->quote_number}";
            
            // Generate PDF
            $pdf = $this->pdfService->generateQuotePdf($quote);
            
            Mail::send('emails.quote', ['quote' => $quote], function ($message) use ($to, $subject, $pdf, $quote) {
                $message->to($to)
                    ->subject($subject)
                    ->attachData($pdf, "quote-{$quote->quote_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });
            
            Log::info("Quote email sent", ['quote_id' => $quote->id, 'to' => $to]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send quote email", [
                'quote_id' => $quote->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send sales order email with PDF attachment
     */
    public function sendSalesOrder(SalesOrder $salesOrder, array $options = []): bool
    {
        try {
            $to = $options['to'] ?? $salesOrder->client->email;
            $subject = $options['subject'] ?? "Sales Order {$salesOrder->so_number}";
            
            // Generate PDF
            $pdf = $this->pdfService->generateSalesOrderPdf($salesOrder);
            
            Mail::send('emails.sales_order', ['salesOrder' => $salesOrder], function ($message) use ($to, $subject, $pdf, $salesOrder) {
                $message->to($to)
                    ->subject($subject)
                    ->attachData($pdf, "sales-order-{$salesOrder->so_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });
            
            Log::info("Sales order email sent", ['sales_order_id' => $salesOrder->id, 'to' => $to]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send sales order email", [
                'sales_order_id' => $salesOrder->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
