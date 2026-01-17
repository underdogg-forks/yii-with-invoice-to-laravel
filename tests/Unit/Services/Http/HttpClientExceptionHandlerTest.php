<?php

namespace Tests\Unit\Services\Http;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use App\Services\Http\Decorators\HttpClientExceptionHandler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(HttpClientExceptionHandler::class)]
class HttpClientExceptionHandlerTest extends TestCase
{
    private ApiClientInterface $mockClient;
    private HttpClientExceptionHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(ApiClientInterface::class);
        $this->handler = new HttpClientExceptionHandler($this->mockClient);
    }

    #[Test]
    public function it_implements_api_client_interface(): void
    {
        /* Assert */
        $this->assertInstanceOf(ApiClientInterface::class, $this->handler);
    }

    #[Test]
    public function it_returns_response_when_request_succeeds(): void
    {
        /* Arrange */
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(false);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET, 'https://api.example.com/test', [])
            ->andReturn($mockResponse);

        /* Act */
        $response = $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertSame($mockResponse, $response);
    }

    #[Test]
    public function it_throws_runtime_exception_for_rate_limit_error(): void
    {
        /* Arrange */
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(true);
        $mockResponse->shouldReceive('status')->andReturn(429);
        $mockResponse->shouldReceive('json')->andReturn(['message' => 'Too many requests']);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Rate limit exceeded: Too many requests');
        $this->expectExceptionCode(429);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_throws_runtime_exception_for_server_error(): void
    {
        /* Arrange */
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(true);
        $mockResponse->shouldReceive('status')->andReturn(500);
        $mockResponse->shouldReceive('json')->andReturn(['error' => 'Internal server error']);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Server error: Internal server error');
        $this->expectExceptionCode(500);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_throws_runtime_exception_for_client_error(): void
    {
        /* Arrange */
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(true);
        $mockResponse->shouldReceive('status')->andReturn(404);
        $mockResponse->shouldReceive('json')->andReturn(['message' => 'Not found']);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Client error: Not found');
        $this->expectExceptionCode(404);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_uses_default_message_when_no_error_message_in_response(): void
    {
        /* Arrange */
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(true);
        $mockResponse->shouldReceive('status')->andReturn(400);
        $mockResponse->shouldReceive('json')->andReturn([]);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Client error: HTTP request failed');
        $this->expectExceptionCode(400);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_rethrows_request_exception(): void
    {
        /* Arrange */
        $requestException = Mockery::mock(RequestException::class);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andThrow($requestException);

        /* Act & Assert */
        $this->expectException(RequestException::class);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_wraps_generic_exceptions_in_runtime_exception(): void
    {
        /* Arrange */
        $genericException = new \Exception('Connection timeout', 0);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andThrow($genericException);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API request failed: Connection timeout');
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_passes_options_to_decorated_client(): void
    {
        /* Arrange */
        $options = [
            'headers' => ['Authorization' => 'Bearer token'],
            'timeout' => 30,
        ];

        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(false);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST, 'https://api.example.com/test', $options)
            ->andReturn($mockResponse);

        /* Act */
        $this->handler->request(HttpMethod::POST, 'https://api.example.com/test', $options);

        /* Assert - handled by shouldReceive */
    }

    #[Test]
    public function it_handles_503_service_unavailable_error(): void
    {
        /* Arrange */
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(true);
        $mockResponse->shouldReceive('status')->andReturn(503);
        $mockResponse->shouldReceive('json')->andReturn(['message' => 'Service temporarily unavailable']);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Server error: Service temporarily unavailable');
        $this->expectExceptionCode(503);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_handles_401_unauthorized_error(): void
    {
        /* Arrange */
        $mockResponse = Mockery::mock(Response::class);
        $mockResponse->shouldReceive('failed')->andReturn(true);
        $mockResponse->shouldReceive('status')->andReturn(401);
        $mockResponse->shouldReceive('json')->andReturn(['error' => 'Unauthorized']);

        $this->mockClient->shouldReceive('request')
            ->once()
            ->andReturn($mockResponse);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Client error: Unauthorized');
        $this->expectExceptionCode(401);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
