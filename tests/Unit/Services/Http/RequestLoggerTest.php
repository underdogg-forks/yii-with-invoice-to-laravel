<?php

namespace Tests\Unit\Services\Http;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use App\Services\Http\Decorators\RequestLogger;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(RequestLogger::class)]
class RequestLoggerTest extends TestCase
{
    private ApiClientInterface $mockClient;
    private RequestLogger $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(ApiClientInterface::class);
        $this->logger = new RequestLogger($this->mockClient);
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
        Log::shouldReceive('info')
            ->once()
            ->with('API Request', Mockery::on(function ($context) {
                return isset($context['request_id']) &&
                       isset($context['method']) &&
                       isset($context['url']) &&
                       $context['method'] === 'GET' &&
                       $context['url'] === 'https://api.example.com/test';
            }));

        Log::shouldReceive('info')
            ->once()
            ->with('API Response', Mockery::any());

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('status')->andReturn(200);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act */
        $result = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
    }

    #[Test]
    public function it_logs_response_after_execution(): void
    {
        /* Arrange */
        Log::shouldReceive('info')
            ->once()
            ->with('API Request', Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('API Response', Mockery::on(function ($context) {
                return isset($context['request_id']) &&
                       isset($context['status']) &&
                       isset($context['duration_ms']) &&
                       $context['status'] === 200;
            }));

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('status')->andReturn(200);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act */
        $result = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertEquals(200, $result->status());
    }

    #[Test]
    public function it_logs_request_headers(): void
    {
        /* Arrange */
        $headers = [
            'Authorization' => 'Bearer token123',
            'Content-Type' => 'application/json',
        ];

        Log::shouldReceive('info')
            ->once()
            ->with('API Request', Mockery::on(function ($context) use ($headers) {
                return $context['headers'] === $headers;
            }));

        Log::shouldReceive('info')
            ->once()
            ->with('API Response', Mockery::any());

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('status')->andReturn(200);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act */
        $result = $this->logger->request(HttpMethod::POST, 'https://api.example.com/test', [
            'headers' => $headers
        ]);

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
    }

    #[Test]
    public function it_calculates_request_duration(): void
    {
        /* Arrange */
        Log::shouldReceive('info')
            ->once()
            ->with('API Request', Mockery::any());

        Log::shouldReceive('info')
            ->once()
            ->with('API Response', Mockery::on(function ($context) {
                return isset($context['duration_ms']) &&
                       is_numeric($context['duration_ms']) &&
                       $context['duration_ms'] >= 0;
            }));

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('status')->andReturn(200);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act */
        $result = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
    }

    #[Test]
    public function it_logs_error_when_request_fails(): void
    {
        /* Arrange */
        $exception = new \RuntimeException('Connection failed');

        Log::shouldReceive('info')
            ->once()
            ->with('API Request', Mockery::any());

        Log::shouldReceive('error')
            ->once()
            ->with('API Request Failed', Mockery::on(function ($context) {
                return isset($context['request_id']) &&
                       isset($context['duration_ms']) &&
                       isset($context['error']) &&
                       $context['error'] === 'Connection failed';
            }));

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andThrow($exception);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Connection failed');
        $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_generates_unique_request_id(): void
    {
        /* Arrange */
        $requestIds = [];

        Log::shouldReceive('info')
            ->twice()
            ->with('API Request', Mockery::on(function ($context) use (&$requestIds) {
                $requestIds[] = $context['request_id'];
                return true;
            }));

        Log::shouldReceive('info')
            ->twice()
            ->with('API Response', Mockery::any());

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('status')->andReturn(200);

        $this->mockClient->shouldReceive('request')
            ->twice()
            ->andReturn($mockResponse);

        /* Act */
        $this->logger->request(HttpMethod::GET, 'https://api.example.com/test1');
        $this->logger->request(HttpMethod::GET, 'https://api.example.com/test2');

        /* Assert */
        $this->assertCount(2, $requestIds);
        $this->assertNotEquals($requestIds[0], $requestIds[1]);
    }

    #[Test]
    public function it_returns_response_from_decorated_client(): void
    {
        /* Arrange */
        Log::shouldReceive('info')->twice();

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('status')->andReturn(200);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET, 'https://api.example.com/test', [])
            ->andReturn($mockResponse);

        /* Act */
        $response = $this->logger->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertSame($mockResponse, $response);
    }

    #[Test]
    public function it_passes_options_to_decorated_client(): void
    {
        /* Arrange */
        Log::shouldReceive('info')->twice();

        $options = [
            'headers' => ['Authorization' => 'Bearer token'],
            'timeout' => 30,
        ];

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('status')->andReturn(200);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST, 'https://api.example.com/test', $options)
            ->andReturn($mockResponse);

        /* Act */
        $result = $this->logger->request(HttpMethod::POST, 'https://api.example.com/test', $options);

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
