<?php

namespace App\Services\Peppol;

use App\Enums\PeppolProvider;

/**
 * Peppyrus API client
 * 
 * Handles authentication and configuration for Peppyrus provider.
 * Uses OAuth2 authentication.
 */
class PeppyrusClient
{
    private ApiClient $apiClient;
    private ?string $accessToken = null;
    private ?int $tokenExpiry = null;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->configure();
    }

    private function configure(): void
    {
        $this->apiClient->setBaseUrl(PeppolProvider::PEPPYRUS->getBaseUrl());
        $this->apiClient->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Obtain OAuth2 access token
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken && $this->tokenExpiry && time() < $this->tokenExpiry) {
            return $this->accessToken;
        }

        $clientId = config('peppol.providers.peppyrus.client_id');
        $clientSecret = config('peppol.providers.peppyrus.client_secret');

        $response = $this->apiClient->request('POST', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);

        $this->accessToken = $response['access_token'];
        $this->tokenExpiry = time() + ($response['expires_in'] ?? 3600) - 60;

        return $this->accessToken;
    }

    public function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }

    /**
     * Make a request using the configured API client with OAuth2 token
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        $token = $this->getAccessToken();
        
        $this->apiClient->setHeaders([
            'Authorization' => "Bearer {$token}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        return $this->apiClient->request($method, $endpoint, $data);
    }
}
