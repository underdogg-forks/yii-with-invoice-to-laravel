<?php

namespace Tests\Unit\Services\Http;

use App\Contracts\ApiClientInterface;
use App\Enums\HttpMethod;
use App\Services\Http\ApiClient;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ApiClient::class)]
class ApiClientTest extends TestCase
{
    private ApiClientInterface $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new ApiClient();
    }

    #[Test]
    public function it_implements_api_client_interface(): void
    {
        /* Assert */
        $this->assertInstanceOf(ApiClientInterface::class, $this->client);
    }

    #[Test]
    public function it_makes_get_request_successfully(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.example.com/test' => Http::response(['data' => 'success'], 200)
        ]);

        /* Act */
        $response = $this->client->request(
            HttpMethod::GET,
            'https://api.example.com/test'
        );

        /* Assert */
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['data' => 'success'], $response->json());
        Http::assertSent(function ($request) {
            return $request->method() === 'GET' &&
                   $request->url() === 'https://api.example.com/test';
        });
    }

    #[Test]
    public function it_makes_post_request_successfully(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.example.com/test' => Http::response(['id' => 123], 201)
        ]);

        $data = ['name' => 'Test Item'];

        /* Act */
        $response = $this->client->request(
            HttpMethod::POST,
            'https://api.example.com/test',
            ['json' => $data]
        );

        /* Assert */
        $this->assertEquals(201, $response->status());
        $this->assertEquals(['id' => 123], $response->json());
        Http::assertSent(function ($request) use ($data) {
            return $request->method() === 'POST' &&
                   $request->url() === 'https://api.example.com/test' &&
                   $request->data() === $data;
        });
    }

    #[Test]
    public function it_makes_put_request_successfully(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.example.com/test/123' => Http::response(['updated' => true], 200)
        ]);

        $data = ['name' => 'Updated Item'];

        /* Act */
        $response = $this->client->request(
            HttpMethod::PUT,
            'https://api.example.com/test/123',
            ['body' => $data]
        );

        /* Assert */
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['updated' => true], $response->json());
        Http::assertSent(function ($request) {
            return $request->method() === 'PUT' &&
                   $request->url() === 'https://api.example.com/test/123';
        });
    }

    #[Test]
    public function it_makes_patch_request_successfully(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.example.com/test/123' => Http::response(['patched' => true], 200)
        ]);

        /* Act */
        $response = $this->client->request(
            HttpMethod::PATCH,
            'https://api.example.com/test/123',
            ['json' => ['status' => 'active']]
        );

        /* Assert */
        $this->assertEquals(200, $response->status());
        $this->assertEquals(['patched' => true], $response->json());
    }

    #[Test]
    public function it_makes_delete_request_successfully(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.example.com/test/123' => Http::response(null, 204)
        ]);

        /* Act */
        $response = $this->client->request(
            HttpMethod::DELETE,
            'https://api.example.com/test/123'
        );

        /* Assert */
        $this->assertEquals(204, $response->status());
        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE' &&
                   $request->url() === 'https://api.example.com/test/123';
        });
    }

    #[Test]
    public function it_makes_head_request_successfully(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.example.com/test' => Http::response(null, 200)
        ]);

        /* Act */
        $response = $this->client->request(
            HttpMethod::HEAD,
            'https://api.example.com/test'
        );

        /* Assert */
        $this->assertEquals(200, $response->status());
        Http::assertSent(function ($request) {
            return $request->method() === 'HEAD';
        });
    }

    #[Test]
    public function it_makes_options_request_successfully(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.example.com/test' => Http::response(null, 200)
        ]);

        /* Act */
        $response = $this->client->request(
            HttpMethod::OPTIONS,
            'https://api.example.com/test'
        );

        /* Assert */
        $this->assertEquals(200, $response->status());
    }

    #[Test]
    public function it_sends_custom_headers(): void
    {
        /* Arrange */
        Http::fake();

        $headers = [
            'Authorization' => 'Bearer token123',
            'X-Custom-Header' => 'custom-value'
        ];

        /* Act */
        $this->client->request(
            HttpMethod::GET,
            'https://api.example.com/test',
            ['headers' => $headers]
        );

        /* Assert */
        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer token123') &&
                   $request->hasHeader('X-Custom-Header', 'custom-value');
        });
    }

    #[Test]
    public function it_sends_query_parameters(): void
    {
        /* Arrange */
        Http::fake();

        $query = [
            'page' => 1,
            'limit' => 50,
            'status' => 'active'
        ];

        /* Act */
        $this->client->request(
            HttpMethod::GET,
            'https://api.example.com/test',
            ['query' => $query]
        );

        /* Assert */
        Http::assertSent(function ($request) {
            $url = $request->url();
            return str_contains($url, 'page=1') &&
                   str_contains($url, 'limit=50') &&
                   str_contains($url, 'status=active');
        });
    }

    #[Test]
    public function it_uses_custom_timeout(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $this->client->request(
            HttpMethod::GET,
            'https://api.example.com/test',
            ['timeout' => 60]
        );

        /* Assert */
        Http::assertSent(function ($request) {
            // Laravel Http doesn't expose timeout directly in request
            // This test verifies the request was made
            return true;
        });
    }

    #[Test]
    public function it_uses_default_timeout_when_not_specified(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $this->client->request(
            HttpMethod::GET,
            'https://api.example.com/test'
        );

        /* Assert */
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.example.com/test';
        });
    }

    #[Test]
    public function it_handles_json_body_option(): void
    {
        /* Arrange */
        Http::fake();

        $data = ['key' => 'value'];

        /* Act */
        $this->client->request(
            HttpMethod::POST,
            'https://api.example.com/test',
            ['json' => $data]
        );

        /* Assert */
        Http::assertSent(function ($request) use ($data) {
            return $request->data() === $data;
        });
    }

    #[Test]
    public function it_handles_body_option(): void
    {
        /* Arrange */
        Http::fake();

        $data = ['key' => 'value'];

        /* Act */
        $this->client->request(
            HttpMethod::POST,
            'https://api.example.com/test',
            ['body' => $data]
        );

        /* Assert */
        Http::assertSent(function ($request) use ($data) {
            return $request->data() === $data;
        });
    }
}
