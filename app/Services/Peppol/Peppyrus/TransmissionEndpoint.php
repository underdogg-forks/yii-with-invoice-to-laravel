<?php

namespace App\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\PeppyrusClient;

/**
 * Peppyrus Transmission Endpoint
 * 
 * Handles document transmission, status checking, and retry operations.
 */
class TransmissionEndpoint
{
    public function __construct(
        private PeppyrusClient $client
    ) {}

    /**
     * Transmit a document via Peppyrus
     *
     * @param array $documentData Document data including UBL XML
     * @return array Response with transmission ID and status
     */
    public function transmitDocument(array $documentData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/transmissions',
            $documentData
        );
    }

    /**
     * Get transmission status
     *
     * @param string $transmissionId Transmission identifier
     * @return array Transmission status information
     */
    public function getTransmissionStatus(string $transmissionId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/transmissions/{$transmissionId}/status"
        );
    }

    /**
     * Retry a failed transmission
     *
     * @param string $transmissionId Transmission identifier
     * @return array Retry result
     */
    public function retryTransmission(string $transmissionId): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            "/api/transmissions/{$transmissionId}/retry"
        );
    }
}
