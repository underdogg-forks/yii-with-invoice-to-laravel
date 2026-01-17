<?php

namespace App\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCoveClient;

/**
 * StoreCove Validation Endpoint
 * 
 * Validates documents, participants, and syntax compliance.
 */
class ValidationEndpoint
{
    public function __construct(
        private StoreCoveClient $client
    ) {}

    /**
     * Validate a document before submission
     *
     * @param array $documentData Document to validate
     * @return array Validation result with errors/warnings
     */
    public function validateDocument(array $documentData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v2/validation/document',
            $documentData
        );
    }

    /**
     * Validate if a participant exists in Peppol network
     *
     * @param string $participantId Participant identifier (e.g., 0123:123456789)
     * @param string $documentType Document type identifier
     * @return array Validation result
     */
    public function validateParticipant(string $participantId, string $documentType): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/validation/participant",
            [
                'participant_id' => $participantId,
                'document_type' => $documentType,
            ]
        );
    }

    /**
     * Validate document syntax (UBL/XML structure)
     *
     * @param string $xmlContent UBL XML content
     * @return array Syntax validation result
     */
    public function validateSyntax(string $xmlContent): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v2/validation/syntax',
            ['xml' => $xmlContent]
        );
    }
}
