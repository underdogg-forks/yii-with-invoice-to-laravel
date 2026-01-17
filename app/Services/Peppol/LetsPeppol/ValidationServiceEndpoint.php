<?php

namespace App\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppolClient;

/**
 * LetsPeppol Validation Service Endpoint
 * 
 * Validates invoices and checks compliance with Peppol standards.
 */
class ValidationServiceEndpoint
{
    public function __construct(
        private LetsPeppolClient $client
    ) {}

    /**
     * Validate an invoice before sending
     *
     * @param array $invoiceData Invoice data to validate
     * @return array Validation result with errors/warnings
     */
    public function validateInvoice(array $invoiceData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/v1/validation/invoice',
            $invoiceData
        );
    }

    /**
     * Check compliance with Peppol BIS specifications
     *
     * @param string $xmlContent UBL XML content
     * @param string $specification Specification to check against (e.g., 'bis3')
     * @return array Compliance check result
     */
    public function checkCompliance(string $xmlContent, string $specification = 'bis3'): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/v1/validation/compliance',
            [
                'xml' => $xmlContent,
                'specification' => $specification,
            ]
        );
    }
}
