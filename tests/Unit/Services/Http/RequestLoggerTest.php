<?php

namespace Tests\Unit\Services\Http;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use App\Services\Http\Decorators\RequestLogger;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeApiClient;
use Tests\TestCase;

#[CoversClass(RequestLogger::class)]
class RequestLoggerTest extends TestCase
{
    private FakeApiClient $fakeClient;
    private RequestLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeApiClient();
        $this->logger = new RequestLogger($this->fakeClient);
    }

    #[Test]
    public function it_implements_api_client_interface(): void
    {
        /* Assert */
        $this->assertInstanceOf(ApiClientInterface::class, $this->logger);
    }

    #[Test]
    public function it_logs_request_before_execution(): void
    {
        /* Arrange */
        Log::fake();
        $response = $this->fakeClient->createResponse(200, ['data' => 'success']);
        $this->fakeClient->setNextResponse($response);

        /* Act */
        $result = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
        Log::assertLogged('info', function ($message, $context) {
            return $message === 'API Request' &&
                   isset($context['request_id']) &&
                   isset($context['method']) &&
                   isset($context['url']) &&
                   $context['method'] === 'GET' &&
                   $context['url'] === 'https://api.example.com/test';
        });
    }

    #[Test]
    public function it_logs_response_after_execution(): void
    {
        /* Arrange */
        Log::fake();
        $response = $this->fakeClient->createResponse(200, ['data' => 'success']);
        $this->fakeClient->setNextResponse($response);

        /* Act */
        $result = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertEquals(200, $result->status());
        Log::assertLogged('info', function ($message, $context) {
            return $message === 'API Response' &&
                   isset($context['request_id']) &&
                   isset($context['status']) &&
                   isset($context['duration_ms']) &&
                   $context['status'] === 200;
        });
    }

    #[Test]
    public function it_logs_request_headers(): void
    {
        /* Arrange */
        Log::fake();
        $headers = [
            'Authorization' => 'Bearer token123',
            'Content-Type' => 'application/json',
        ];
        $response = $this->fakeClient->createResponse(200, ['data' => 'success']);
        $this->fakeClient->setNextResponse($response);

        /* Act */
        $result = $this->logger->request(HttpMethod::POST, 'https://api.example.com/test', [
            'headers' => $headers
        ]);

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
        Log::assertLogged('info', function ($message, $context) use ($headers) {
            return $message === 'API Request' && $context['headers'] === $headers;
        });
    }

    #[Test]
    public function it_calculates_request_duration(): void
    {
        /* Arrange */
        Log::fake();
        $response = $this->fakeClient->createResponse(200, ['data' => 'success']);
        $this->fakeClient->setNextResponse($response);

        /* Act */
        $result = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
        Log::assertLogged('info', function ($message, $context) {
            return $message === 'API Response' &&
                   isset($context['duration_ms']) &&
                   is_numeric($context['duration_ms']) &&
                   $context['duration_ms'] >= 0;
        });
    }

    #[Test]
    public function it_logs_error_when_request_fails(): void
    {
        /* Arrange */
        Log::fake();
        $exception = new \RuntimeException('Connection failed');
        $this->fakeClient->setNextException($exception);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Connection failed');
        
        try {
            $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');
        } catch (\RuntimeException $e) {
            Log::assertLogged('error', function ($message, $context) {
                return $message === 'API Request Failed' &&
                       isset($context['request_id']) &&
                       isset($context['duration_ms']) &&
                       isset($context['error']) &&
                       $context['error'] === 'Connection failed';
            });
            throw $e;
        }
    }

    #[Test]
    public function it_generates_unique_request_id(): void
    {
        /* Arrange */
        Log::fake();
        $response = $this->fakeClient->createResponse(200, ['data' => 'success']);
        
        /* Act */
        $this->fakeClient->setNextResponse($response);
        $this->logger->request(HttpMethod::GET, 'https://api.example.com/test1');
        
        $this->fakeClient->setNextResponse($response);
        $this->logger->request(HttpMethod::GET, 'https://api.example.com/test2');

        /* Assert */
        $requestIds = [];
        Log::assertLogged('info', function ($message, $context) use (&$requestIds) {
            if ($message === 'API Request') {
                $requestIds[] = $context['request_id'];
            }
            return true;
        });
        
        $this->assertCount(2, $requestIds);
        $this->assertNotEquals($requestIds[0], $requestIds[1]);
    }

    #[Test]
    public function it_returns_response_from_decorated_client(): void
    {
        /* Arrange */
        Log::fake();
        $response = $this->fakeClient->createResponse(200, ['data' => 'test']);
        $this->fakeClient->setNextResponse($response);

        /* Act */
        $result = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->status());
    }

    #[Test]
    public function it_passes_options_to_decorated_client(): void
    {
        /* Arrange */
        Log::fake();
        $options = [
            'headers' => ['Authorization' => 'Bearer token'],
            'timeout' => 30,
        ];
        $response = $this->fakeClient->createResponse(200, ['data' => 'test']);
        $this->fakeClient->setNextResponse($response);

        /* Act */
        $result = $this->logger->request(HttpMethod::POST, 'https://api.example.com/test', $options);

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
        
        // Verify options were passed to the decorated client
        $lastRequest = $this->fakeClient->getLastRequest();
        $this->assertEquals($options, $lastRequest['options']);
    }
}
