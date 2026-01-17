<?php

namespace App\Services\Peppol;

use App\Enums\PeppolProvider;

/**
 * LetsPeppol API client
 * 
 * Handles authentication and configuration for LetsPeppol provider.
 * Uses X-API-Key header authentication.
 */
class LetsPeppolClient
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->configure();
    }

    private function configure(): void
    {
        $apiKey = config('peppol.providers.letspeppol.api_key');
        
        $this->apiClient->setBaseUrl(PeppolProvider::LETSPEPPOL->getBaseUrl());
        $this->apiClient->setHeaders([
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
