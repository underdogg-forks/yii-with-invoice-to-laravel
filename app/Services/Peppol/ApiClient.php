<?php

namespace App\Services\Peppol;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use App\Services\Http\ApiClient as HttpApiClient;
use Illuminate\Http\Client\Response;

/**
 * Peppol API Client Wrapper
 * 
 * Wraps the base HTTP ApiClient and adds provider-specific configuration
 * capabilities (base URL and headers) needed by Peppol provider clients.
 * 
 * This adapter pattern allows provider clients to configure base URL and
 * headers once, then make multiple requests without repeating configuration.
 */
class ApiClient
{
    private string $baseUrl = '';
    private array $headers = [];
    
    public function __construct(
        private HttpApiClient $httpClient
    ) {}

    /**
     * Set the base URL for all requests
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Set headers for all requests
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Make a request using the configured base URL and headers
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $endpoint Endpoint path (will be appended to base URL)
     * @param array $data Request data
     * @return array Response data as array
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        $options = [
            'headers' => $this->headers,
            'json' => $data,
        ];
        
        // Convert string method to HttpMethod enum
        $httpMethod = HttpMethod::from(strtoupper($method));
        
        $response = $this->httpClient->request($httpMethod, $url, $options);
        
        return $response->json() ?? [];
    }

    /**
     * Get the underlying HTTP client (for testing)
     */
    public function getHttpClient(): HttpApiClient
    {
        return $this->httpClient;
    }
}
