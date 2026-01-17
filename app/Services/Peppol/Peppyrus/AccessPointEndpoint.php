<?php

namespace App\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\PeppyrusClient;

/**
 * Peppyrus Access Point Endpoint
 * 
 * Queries access point information and metadata.
 */
class AccessPointEndpoint
{
    public function __construct(
        private PeppyrusClient $client
    ) {}

    /**
     * Query access point for a participant
     *
     * @param string $participantId Participant identifier
     * @param string $documentType Document type identifier
     * @return array Access point information
     */
    public function queryAccessPoint(string $participantId, string $documentType): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            '/api/access-points/query',
            [
                'participant_id' => $participantId,
                'document_type' => $documentType,
            ]
        );
    }

    /**
     * Get access point metadata
     *
     * @param string $accessPointId Access point identifier
     * @return array Access point metadata
     */
    public function getAccessPointMetadata(string $accessPointId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/access-points/{$accessPointId}/metadata"
        );
    }
}
