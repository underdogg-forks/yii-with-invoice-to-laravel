<?php

namespace App\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCoveClient;

/**
 * StoreCove Delivery Status Endpoint
 * 
 * Handles delivery status tracking, history, and acknowledgments.
 */
class DeliveryStatusEndpoint
{
    public function __construct(
        private StoreCoveClient $client
    ) {}

    /**
     * Get current delivery status
     *
     * @param string $documentId Document identifier
     * @return array Current delivery status
     */
    public function getDeliveryStatus(string $documentId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/document_submissions/{$documentId}/delivery_status"
        );
    }

    /**
     * Get complete delivery history
     *
     * @param string $documentId Document identifier
     * @return array Array of delivery events
     */
    public function getDeliveryHistory(string $documentId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/document_submissions/{$documentId}/delivery_history"
        );
    }

    /**
     * Check if recipient has acknowledged the document
     *
     * @param string $documentId Document identifier
     * @return array Acknowledgment status and details
     */
    public function checkRecipientAcknowledgment(string $documentId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/document_submissions/{$documentId}/acknowledgment"
        );
    }
}
