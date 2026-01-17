<?php

namespace App\Contracts;

use App\Enums\HttpMethod;
use Illuminate\Http\Client\Response;

interface ApiClientInterface
{
    /**
     * Make an HTTP request
     *
     * @param HttpMethod $method HTTP method to use
     * @param string $url The URL to request
     * @param array $options Request options (headers, body, query, timeout, etc.)
     * @return Response
     */
    public function request(HttpMethod $method, string $url, array $options = []): Response;
}
