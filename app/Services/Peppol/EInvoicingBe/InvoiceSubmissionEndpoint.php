<?php

namespace App\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBeClient;

/**
 * eInvoicing.be Invoice Submission Endpoint
 * 
 * Handles invoice submission, status checking, and cancellation for Belgian market.
 */
class InvoiceSubmissionEndpoint
{
    public function __construct(
        private EInvoicingBeClient $client
    ) {}

    /**
     * Submit an invoice to eInvoicing.be
     *
     * @param array $invoiceData Invoice data including UBL XML
     * @return array Response with submission ID and status
     */
    public function submitInvoice(array $invoiceData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v1/invoices/submit',
            $invoiceData
        );
    }

    /**
     * Get submission status
     *
     * @param string $submissionId Submission identifier
     * @return array Submission status information
     */
    public function getSubmissionStatus(string $submissionId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v1/invoices/submissions/{$submissionId}/status"
        );
    }

    /**
     * Cancel a submitted invoice
     *
     * @param string $submissionId Submission identifier
     * @return array Cancellation confirmation
     */
    public function cancelSubmission(string $submissionId): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            "/api/v1/invoices/submissions/{$submissionId}/cancel"
        );
    }
}
