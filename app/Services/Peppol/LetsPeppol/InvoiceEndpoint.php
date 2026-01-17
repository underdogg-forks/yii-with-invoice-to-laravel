<?php

namespace App\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppolClient;

/**
 * LetsPeppol Invoice Endpoint
 * 
 * Handles invoice sending, status checking, and cancellation.
 */
class InvoiceEndpoint
{
    public function __construct(
        private LetsPeppolClient $client
    ) {}

    /**
     * Send an invoice via LetsPeppol
     *
     * @param array $invoiceData Invoice data including UBL XML
     * @return array Response with invoice ID and status
     */
    public function sendInvoice(array $invoiceData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/v1/invoices',
            $invoiceData
        );
    }

    /**
     * Get invoice status
     *
     * @param string $invoiceId Invoice identifier
     * @return array Invoice status information
     */
    public function getInvoiceStatus(string $invoiceId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/v1/invoices/{$invoiceId}/status"
        );
    }

    /**
     * Cancel a sent invoice
     *
     * @param string $invoiceId Invoice identifier
     * @return array Cancellation confirmation
     */
    public function cancelInvoice(string $invoiceId): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            "/v1/invoices/{$invoiceId}/cancel"
        );
    }
}
