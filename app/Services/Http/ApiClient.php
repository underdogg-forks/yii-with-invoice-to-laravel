<?php

namespace App\Services\Http;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiClient implements ApiClientInterface
{
    public function request(HttpMethod $method, string $url, array $options = []): Response
    {
        $timeout = $options['timeout'] ?? 30;
        $headers = $options['headers'] ?? [];
        $body = $options['body'] ?? $options['json'] ?? [];
        $query = $options['query'] ?? [];

        $request = Http::timeout($timeout)
            ->withHeaders($headers);

        if (!empty($query)) {
            $request = $request->withQueryParameters($query);
        }

        return match($method) {
            HttpMethod::GET => $request->get($url),
            HttpMethod::POST => $request->post($url, $body),
            HttpMethod::PUT => $request->put($url, $body),
            HttpMethod::PATCH => $request->patch($url, $body),
            HttpMethod::DELETE => $request->delete($url, $body),
            HttpMethod::HEAD => $request->head($url),
            HttpMethod::OPTIONS => $request->send('OPTIONS', $url),
        };
    }
}
