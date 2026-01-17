<?php

namespace Tests\Fakes;

/**
 * Fake StoreCoveClient for testing
 * 
 * Test double for the StoreCove client with request recording
 * and configurable responses. Preferred over Mockery mocks.
 */
class FakeStoreCoveClient
{
    public array $requests = [];
    private array $responses = [];
    private int $responseIndex = 0;

    /**
     * Record a request and return pre-configured response
     */
    public function request(string $method, string $endpoint, array $data = []): array
    {
        // Record the request
        $this->requests[] = [
            'method' => $method,
            'endpoint' => $endpoint,
            'data' => $data,
        ];

        // Return next configured response or empty array
        if (isset($this->responses[$this->responseIndex])) {
            $response = $this->responses[$this->responseIndex];
            $this->responseIndex++;
            return $response;
        }

        return [];
    }

    /**
     * Set the next response to return
     */
    public function addResponse(array $response): void
    {
        $this->responses[] = $response;
    }

    /**
     * Set multiple responses at once
     */
    public function setResponses(array $responses): void
    {
        $this->responses = $responses;
        $this->responseIndex = 0;
    }

    /**
     * Check if a specific request was made
     */
    public function hasRequest(string $method, string $endpoint): bool
    {
        foreach ($this->requests as $request) {
            if ($request['method'] === $method && $request['endpoint'] === $endpoint) {
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
     * Get request by index
     */
    public function getRequest(int $index): ?array
    {
        return $this->requests[$index] ?? null;
    }

    /**
     * Count total requests made
     */
    public function countRequests(): int
    {
        return count($this->requests);
    }

    /**
     * Reset all recorded data
     */
    public function reset(): void
    {
        $this->requests = [];
        $this->responses = [];
        $this->responseIndex = 0;
    }
}
