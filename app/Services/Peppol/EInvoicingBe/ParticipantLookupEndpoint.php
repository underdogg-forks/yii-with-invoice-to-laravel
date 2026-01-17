<?php

namespace App\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBeClient;

/**
 * eInvoicing.be Participant Lookup Endpoint
 * 
 * Looks up Belgian participants in Peppol network.
 */
class ParticipantLookupEndpoint
{
    public function __construct(
        private EInvoicingBeClient $client
    ) {}

    /**
     * Lookup a Belgian participant
     *
     * @param string $participantId Participant identifier (e.g., 0208:BE0123456789)
     * @return array Participant information
     */
    public function lookupParticipant(string $participantId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v1/participants/{$participantId}"
        );
    }

    /**
     * Get Belgian endpoint details for a participant
     *
     * @param string $vatNumber Belgian VAT number
     * @return array Endpoint details
     */
    public function getBelgianEndpoint(string $vatNumber): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v1/participants/belgian/{$vatNumber}/endpoint"
        );
    }
}
