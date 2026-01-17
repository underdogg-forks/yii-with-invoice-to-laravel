<?php

namespace App\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBeClient;

/**
 * eInvoicing.be Status Tracking Endpoint
 * 
 * Tracks invoice status and retrieves status history.
 */
class StatusTrackingEndpoint
{
    public function __construct(
        private EInvoicingBeClient $client
    ) {}

    /**
     * Track current status of an invoice
     *
     * @param string $invoiceId Invoice identifier
     * @return array Current status information
     */
    public function trackStatus(string $invoiceId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v1/tracking/{$invoiceId}/status"
        );
    }

    /**
     * Get complete status history
     *
     * @param string $invoiceId Invoice identifier
     * @return array Array of status events
     */
    public function getStatusHistory(string $invoiceId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v1/tracking/{$invoiceId}/history"
        );
    }
}
