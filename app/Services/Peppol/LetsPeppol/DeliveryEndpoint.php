<?php

namespace App\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppolClient;

/**
 * LetsPeppol Delivery Endpoint
 * 
 * Tracks delivery status and retrieves delivery reports.
 */
class DeliveryEndpoint
{
    public function __construct(
        private LetsPeppolClient $client
    ) {}

    /**
     * Get delivery status for an invoice
     *
     * @param string $invoiceId Invoice identifier
     * @return array Current delivery status
     */
    public function getDeliveryStatus(string $invoiceId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/v1/deliveries/{$invoiceId}/status"
        );
    }

    /**
     * Get complete delivery report
     *
     * @param string $invoiceId Invoice identifier
     * @return array Detailed delivery report
     */
    public function getDeliveryReport(string $invoiceId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/v1/deliveries/{$invoiceId}/report"
        );
    }
}
