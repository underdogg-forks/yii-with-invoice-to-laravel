<?php

namespace App\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\PeppyrusClient;

/**
 * Peppyrus Compliance Endpoint
 * 
 * Validates compliance and retrieves validation reports.
 */
class ComplianceEndpoint
{
    public function __construct(
        private PeppyrusClient $client
    ) {}

    /**
     * Validate document compliance
     *
     * @param array $documentData Document to validate
     * @return array Compliance validation result
     */
    public function validateCompliance(array $documentData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/compliance/validate',
            $documentData
        );
    }

    /**
     * Get validation report
     *
     * @param string $validationId Validation identifier
     * @return array Detailed validation report
     */
    public function getValidationReport(string $validationId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/compliance/reports/{$validationId}"
        );
    }
}
