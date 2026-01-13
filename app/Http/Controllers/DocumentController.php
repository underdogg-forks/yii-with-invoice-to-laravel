<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\SalesOrder;
use App\Services\PdfService;
use App\Services\UblXmlService;
use App\Services\PeppolService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(
        private PdfService $pdfService,
        private UblXmlService $ublXmlService,
        private PeppolService $peppolService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:manage-invoices')->only(['invoicePdf', 'invoiceXml', 'sendInvoicePeppol']);
        $this->middleware('permission:manage-quotes')->only(['quotePdf', 'salesOrderPdf']);
    }

    /**
     * Download invoice PDF
     */
    public function invoicePdf(int $id)
    {
        $invoice = Invoice::with(['client', 'items', 'amounts'])->findOrFail($id);
        
        $pdf = $this->pdfService->generateInvoicePdf($invoice);
        $filename = "invoice-{$invoice->invoice_number}.pdf";
        
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download invoice UBL XML
     */
    public function invoiceXml(int $id)
    {
        $invoice = Invoice::with(['client', 'items', 'amounts'])->findOrFail($id);
        
        $xml = $this->ublXmlService->generateInvoiceXml($invoice);
        $filename = "invoice-{$invoice->invoice_number}.xml";
        
        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Send invoice via Peppol network
     */
    public function sendInvoicePeppol(int $id)
    {
        $invoice = Invoice::with(['client', 'items', 'amounts'])->findOrFail($id);
        
        try {
            $response = $this->peppolService->sendInvoice($invoice);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice sent via Peppol network successfully',
                'document_id' => $response['document_id'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice via Peppol network',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download quote PDF
     */
    public function quotePdf(int $id)
    {
        $quote = Quote::with(['client', 'status'])->findOrFail($id);
        
        $pdf = $this->pdfService->generateQuotePdf($quote);
        $filename = "quote-{$quote->quote_number}.pdf";
        
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download sales order PDF
     */
    public function salesOrderPdf(int $id)
    {
        $salesOrder = SalesOrder::with(['client', 'status'])->findOrFail($id);
        
        $pdf = $this->pdfService->generateSalesOrderPdf($salesOrder);
        $filename = "sales-order-{$salesOrder->so_number}.pdf";
        
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
