<?php

namespace App\Services\Http\Decorators;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\RequestException;

class HttpClientExceptionHandler implements ApiClientInterface
{
    public function __construct(
        private ApiClientInterface $client
    ) {}

    public function request(HttpMethod $method, string $url, array $options = []): Response
    {
        try {
            $response = $this->client->request($method, $url, $options);

            // Check for HTTP errors
            if ($response->failed()) {
                $this->handleHttpError($response);
            }

            return $response;
        } catch (RequestException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException(
                'API request failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    private function handleHttpError(Response $response): void
    {
        $status = $response->status();
        $body = $response->json() ?? [];
        $message = $body['message'] ?? $body['error'] ?? 'HTTP request failed';

        match(true) {
            $status === 429 => throw new \RuntimeException('Rate limit exceeded: ' . $message, 429),
            $status >= 500 => throw new \RuntimeException('Server error: ' . $message, $status),
            $status >= 400 => throw new \RuntimeException('Client error: ' . $message, $status),
            default => throw new \RuntimeException($message, $status),
        };
    }
}
