<?php

namespace App\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\PeppyrusClient;

/**
 * Peppyrus Acknowledgment Endpoint
 * 
 * Retrieves acknowledgments and their details.
 */
class AcknowledgmentEndpoint
{
    public function __construct(
        private PeppyrusClient $client
    ) {}

    /**
     * Get acknowledgment for a transmission
     *
     * @param string $transmissionId Transmission identifier
     * @return array Acknowledgment information
     */
    public function getAcknowledgment(string $transmissionId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/transmissions/{$transmissionId}/acknowledgment"
        );
    }

    /**
     * Get detailed acknowledgment information
     *
     * @param string $acknowledgmentId Acknowledgment identifier
     * @return array Complete acknowledgment details
     */
    public function getAcknowledgmentDetails(string $acknowledgmentId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/acknowledgments/{$acknowledgmentId}"
        );
    }
}
