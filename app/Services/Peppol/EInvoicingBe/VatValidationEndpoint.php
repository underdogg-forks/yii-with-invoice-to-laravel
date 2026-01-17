<?php

namespace App\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBeClient;

/**
 * eInvoicing.be VAT Validation Endpoint
 * 
 * Validates Belgian VAT numbers and retrieves VAT details.
 */
class VatValidationEndpoint
{
    public function __construct(
        private EInvoicingBeClient $client
    ) {}

    /**
     * Validate a Belgian VAT number
     *
     * @param string $vatNumber VAT number to validate (e.g., BE0123456789)
     * @return array Validation result
     */
    public function validateVatNumber(string $vatNumber): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v1/vat/validate',
            ['vat_number' => $vatNumber]
        );
    }

    /**
     * Get VAT details for a company
     *
     * @param string $vatNumber VAT number
     * @return array Company and VAT details
     */
    public function getVatDetails(string $vatNumber): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v1/vat/{$vatNumber}/details"
        );
    }
}
