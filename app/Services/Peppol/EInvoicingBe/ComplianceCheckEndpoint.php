<?php

namespace App\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBeClient;

/**
 * eInvoicing.be Compliance Check Endpoint
 * 
 * Validates Belgian-specific compliance and document structure.
 */
class ComplianceCheckEndpoint
{
    public function __construct(
        private EInvoicingBeClient $client
    ) {}

    /**
     * Check Belgian compliance requirements
     *
     * @param array $invoiceData Invoice data to check
     * @return array Compliance check result
     */
    public function checkBelgianCompliance(array $invoiceData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v1/compliance/check',
            $invoiceData
        );
    }

    /**
     * Validate document structure
     *
     * @param string $xmlContent UBL XML content
     * @return array Structure validation result
     */
    public function validateStructure(string $xmlContent): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v1/compliance/validate-structure',
            ['xml' => $xmlContent]
        );
    }
}
