<?php

namespace App\Services\Http\Decorators;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestLogger implements ApiClientInterface
{
    public function __construct(
        private ApiClientInterface $client
    ) {}

    public function request(HttpMethod $method, string $url, array $options = []): Response
    {
        $requestId = Str::uuid()->toString();
        $startTime = microtime(true);

        Log::info('API Request', [
            'request_id' => $requestId,
            'method' => $method->value,
            'url' => $url,
            'headers' => $options['headers'] ?? [],
        ]);

        try {
            $response = $this->client->request($method, $url, $options);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('API Response', [
                'request_id' => $requestId,
                'status' => $response->status(),
                'duration_ms' => $duration,
            ]);

            return $response;
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('API Request Failed', [
                'request_id' => $requestId,
                'duration_ms' => $duration,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
