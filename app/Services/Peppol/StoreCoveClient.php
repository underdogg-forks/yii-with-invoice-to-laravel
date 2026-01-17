<?php

namespace App\Services\Peppol;

use App\Enums\PeppolProvider;

/**
 * StoreCove API client
 * 
 * Handles authentication and configuration for StoreCove provider.
 * Uses Bearer token authentication.
 */
class StoreCoveClient
{
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->configure();
    }

    private function configure(): void
    {
        $apiKey = config('peppol.providers.storecove.api_key');
        
        $this->apiClient->setBaseUrl(PeppolProvider::STORECOVE->getBaseUrl());
        $this->apiClient->setHeaders([
            'Authorization' => "Bearer {$apiKey}",
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
