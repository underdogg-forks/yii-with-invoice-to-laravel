<?php

namespace Tests\Fakes;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Fake ApiClient for testing
 * 
 * This is a test double that implements ApiClientInterface with real behavior
 * instead of using mocks. Preferred over Mockery mocks for better test clarity.
 */
class FakeApiClient implements ApiClientInterface
{
    public array $requests = [];
    private ?Response $nextResponse = null;
    private ?\Exception $nextException = null;

    public function request(HttpMethod $method, string $url, array $options = []): Response
    {
        // Record the request
        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'options' => $options,
        ];

        // Throw exception if set
        if ($this->nextException) {
            $exception = $this->nextException;
            $this->nextException = null;
            throw $exception;
        }

        // Return pre-configured response or create a default one
        if ($this->nextResponse) {
            $response = $this->nextResponse;
            $this->nextResponse = null;
            return $response;
        }

        // Default successful response
        return $this->createResponse(200, ['success' => true]);
    }

    /**
     * Set the next response to return
     */
    public function setNextResponse(Response $response): void
    {
        $this->nextResponse = $response;
    }

    /**
     * Set the next exception to throw
     */
    public function setNextException(\Exception $exception): void
    {
        $this->nextException = $exception;
    }

    /**
     * Helper to create a response
     */
    public function createResponse(int $status, array $data = [], array $headers = []): Response
    {
        // Use Http::fake() to create a real Response object
        Http::fake([
            '*' => Http::response($data, $status, $headers)
        ]);
        
        $response = Http::get('http://test.local');
        Http::fake(); // Reset fake
        
        return $response;
    }

    /**
     * Check if a request was made with specific parameters
     */
    public function hasRequest(HttpMethod $method, string $url): bool
    {
        foreach ($this->requests as $request) {
            if ($request['method'] === $method && $request['url'] === $url) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all recorded requests
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    /**
     * Get the last request made
     */
    public function getLastRequest(): ?array
    {
        return end($this->requests) ?: null;
    }

    /**
     * Reset all recorded requests
     */
    public function reset(): void
    {
        $this->requests = [];
        $this->nextResponse = null;
        $this->nextException = null;
    }
}
