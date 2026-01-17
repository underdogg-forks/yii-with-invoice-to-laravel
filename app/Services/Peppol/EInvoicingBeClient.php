<?php

namespace App\Services\Peppol;

use App\Enums\PeppolProvider;

/**
 * eInvoicing.be API client
 * 
 * Handles authentication and configuration for eInvoicing.be provider.
 * Uses Bearer token + API key authentication.
 */
class EInvoicingBeClient
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->configure();
    }

    private function configure(): void
    {
        $apiKey = config('peppol.providers.einvoicing_be.api_key');
        $bearerToken = config('peppol.providers.einvoicing_be.bearer_token');
        
        $this->apiClient->setBaseUrl(PeppolProvider::EINVOICING_BE->getBaseUrl());
        $this->apiClient->setHeaders([
            'Authorization' => "Bearer {$bearerToken}",
            'X-API-Key' => $apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);
    }

    public function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }

    /**
     * Make a request using the configured API client
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        return $this->apiClient->request($method, $endpoint, $data);
    }
}
