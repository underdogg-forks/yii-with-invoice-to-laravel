<?php

namespace App\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppolClient;

/**
 * LetsPeppol Participant Endpoint
 * 
 * Handles participant lookup, details retrieval, and endpoint validation.
 */
class ParticipantEndpoint
{
    public function __construct(
        private LetsPeppolClient $client
    ) {}

    /**
     * Lookup a participant in Peppol network
     *
     * @param string $participantId Participant identifier (e.g., 0123:123456789)
     * @return array Participant information
     */
    public function lookupParticipant(string $participantId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/v1/participants/{$participantId}"
        );
    }

    /**
     * Get detailed participant information
     *
     * @param string $participantId Participant identifier
     * @return array Complete participant details
     */
    public function getParticipantDetails(string $participantId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/v1/participants/{$participantId}/details"
        );
    }

    /**
     * Validate participant endpoint
     *
     * @param string $participantId Participant identifier
     * @param string $documentType Document type identifier
     * @return array Validation result
     */
    public function validateEndpoint(string $participantId, string $documentType): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/v1/participants/validate',
            [
                'participant_id' => $participantId,
                'document_type' => $documentType,
            ]
        );
    }
}
