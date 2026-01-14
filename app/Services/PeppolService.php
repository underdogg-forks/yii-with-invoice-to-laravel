<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class PeppolService
{
    public function __construct(
        private UblXmlService $ublXmlService,
        private StoreCoveService $storeCoveService
    ) {}

    /**
     * Send invoice via Peppol network
     *
     * @param Invoice $invoice
     * @return array Response with document_id
     * @throws \Exception
     */
    public function sendInvoice(Invoice $invoice): array
    {
        // Generate UBL XML
        $xml = $this->ublXmlService->generateInvoiceXml($invoice);
        
        // Get recipient Peppol endpoint from client
        $recipientEndpoint = $invoice->client->peppol_endpoint_id ?? null;
        $recipientScheme = $invoice->client->peppol_scheme_id ?? config('peppol.supplier.scheme_id');
        
        if (!$recipientEndpoint) {
            throw new \Exception('Client does not have a Peppol endpoint ID configured');
        }
        
        // Send via StoreCove
        try {
            $response = $this->storeCoveService->sendInvoice($xml, $recipientEndpoint, $recipientScheme);
            
            Log::info('Invoice sent via Peppol', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'recipient_endpoint' => $recipientEndpoint,
                'document_id' => $response['document_id'] ?? null,
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to send invoice via Peppol', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Check if invoice can be sent via Peppol
     *
     * @param Invoice $invoice
     * @return bool
     */
    public function canSendInvoice(Invoice $invoice): bool
    {
        // Check if client has Peppol endpoint
        if (empty($invoice->client->peppol_endpoint_id)) {
            return false;
        }
        
        // Check if invoice is finalized (not draft)
        if ($invoice->status->name === 'draft') {
            return false;
        }
        
        // Check if required fields are present
        if (empty($invoice->invoice_number) || empty($invoice->total_amount)) {
            return false;
        }
        
        return true;
    }
}
