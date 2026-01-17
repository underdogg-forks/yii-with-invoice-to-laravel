<?php

namespace App\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCoveClient;

/**
 * StoreCove Legal Entities Endpoint
 * 
 * Manages legal entities (organizations) registered in StoreCove.
 */
class LegalEntitiesEndpoint
{
    public function __construct(
        private StoreCoveClient $client
    ) {}

    /**
     * Create a new legal entity
     *
     * @param array $entityData Legal entity data (name, identifiers, etc.)
     * @return array Created legal entity with ID
     */
    public function createLegalEntity(array $entityData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v2/legal_entities',
            $entityData
        );
    }

    /**
     * Get legal entity details
     *
     * @param string $entityId Legal entity identifier
     * @return array Legal entity data
     */
    public function getLegalEntity(string $entityId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/legal_entities/{$entityId}"
        );
    }

    /**
     * Update legal entity
     *
     * @param string $entityId Legal entity identifier
     * @param array $entityData Updated data
     * @return array Updated legal entity
     */
    public function updateLegalEntity(string $entityId, array $entityData): array
    {
        return $this->client->request(
            HttpMethod::PUT->value,
            "/api/v2/legal_entities/{$entityId}",
            $entityData
        );
    }

    /**
     * List all legal entities
     *
     * @param array $filters Optional filters (page, per_page, etc.)
     * @return array Array of legal entities
     */
    public function listLegalEntities(array $filters = []): array
    {
        $queryString = !empty($filters) ? '?' . http_build_query($filters) : '';
        
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/legal_entities{$queryString}"
        );
    }
}
