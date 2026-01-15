<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityHeadersMiddlewareTest extends TestCase
{
    #[Test]
    public function it_adds_security_headers_to_response(): void
    {
        // Arrange
        Config::set('security.headers', [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
        ]);

        $middleware = new SecurityHeadersMiddleware();
        $request = Request::create('/test', 'GET');

        // Act
        $response = $middleware->handle($request, function ($req) {
            return new Response('Test content');
        });

        // Assert
        $this->assertEquals('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertEquals('1; mode=block', $response->headers->get('X-XSS-Protection'));
    }

    #[Test]
    public function it_applies_all_configured_headers(): void
    {
        // Arrange
        $headers = [
            'X-Frame-Options' => 'DENY',
            'Strict-Transport-Security' => 'max-age=31536000',
        ];
        
        Config::set('security.headers', $headers);

        $middleware = new SecurityHeadersMiddleware();
        $request = Request::create('/test', 'GET');

        // Act
        $response = $middleware->handle($request, function ($req) {
            return new Response('Test content');
        });

        // Assert
        foreach ($headers as $key => $value) {
            $this->assertEquals($value, $response->headers->get($key));
        }
    }
}
