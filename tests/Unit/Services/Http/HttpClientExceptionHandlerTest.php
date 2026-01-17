<?php

namespace Tests\Unit\Services\Http;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use App\Services\Http\Decorators\HttpClientExceptionHandler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeApiClient;
use Tests\TestCase;

#[CoversClass(HttpClientExceptionHandler::class)]
class HttpClientExceptionHandlerTest extends TestCase
{
    private FakeApiClient $fakeClient;
    private HttpClientExceptionHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeApiClient();
        $this->handler = new HttpClientExceptionHandler($this->fakeClient);
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
        $successResponse = $this->fakeClient->createResponse(200, ['success' => true]);
        $this->fakeClient->setNextResponse($successResponse);

        /* Act */
        $response = $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');

        /* Assert */
        $this->assertSame($successResponse, $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET, 'https://api.example.com/test'));
    }

    #[Test]
    public function it_throws_runtime_exception_for_rate_limit_error(): void
    {
        /* Arrange */
        $errorResponse = $this->fakeClient->createResponse(429, ['message' => 'Too many requests']);
        $this->fakeClient->setNextResponse($errorResponse);

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
        $errorResponse = $this->fakeClient->createResponse(500, ['error' => 'Internal server error']);
        $this->fakeClient->setNextResponse($errorResponse);

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
        $errorResponse = $this->fakeClient->createResponse(404, ['message' => 'Not found']);
        $this->fakeClient->setNextResponse($errorResponse);

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
        $errorResponse = $this->fakeClient->createResponse(400, []);
        $this->fakeClient->setNextResponse($errorResponse);

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
        $requestException = new RequestException(
            $this->fakeClient->createResponse(500)
        );
        $this->fakeClient->setNextException($requestException);

        /* Act & Assert */
        $this->expectException(RequestException::class);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }

    #[Test]
    public function it_wraps_generic_exceptions_in_runtime_exception(): void
    {
        /* Arrange */
        $genericException = new \Exception('Connection timeout', 0);
        $this->fakeClient->setNextException($genericException);

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

        $successResponse = $this->fakeClient->createResponse(200, ['success' => true]);
        $this->fakeClient->setNextResponse($successResponse);

        /* Act */
        $result = $this->handler->request(HttpMethod::POST, 'https://api.example.com/test', $options);

        /* Assert */
        $this->assertInstanceOf(Response::class, $result);
        $lastRequest = $this->fakeClient->getLastRequest();
        $this->assertSame($options, $lastRequest['options']);
    }

    #[Test]
    public function it_handles_503_service_unavailable_error(): void
    {
        /* Arrange */
        $errorResponse = $this->fakeClient->createResponse(503, ['message' => 'Service temporarily unavailable']);
        $this->fakeClient->setNextResponse($errorResponse);

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
        $errorResponse = $this->fakeClient->createResponse(401, ['error' => 'Unauthorized']);
        $this->fakeClient->setNextResponse($errorResponse);

        /* Act & Assert */
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Client error: Unauthorized');
        $this->expectExceptionCode(401);
        $this->handler->request(HttpMethod::GET, 'https://api.example.com/test');
    }
}
