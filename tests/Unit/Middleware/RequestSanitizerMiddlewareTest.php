<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RequestSanitizerMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RequestSanitizerMiddlewareTest extends TestCase
{
    public function it_sanitizes_input_data(): void
    {
        // Arrange
        Config::set('security.sanitize.enabled', true);
        Config::set('security.sanitize.whitelist_fields', []);

        $middleware = new RequestSanitizerMiddleware();
        $request = Request::create('/test', 'POST', [
            'name' => '  John Doe  ',
            'description' => '<script>alert("xss")</script>Normal text',
        ]);

        // Act
        $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        // Assert
        $this->assertEquals('John Doe', $request->input('name'));
        $this->assertEquals('Normal text', $request->input('description'));
    }

    public function it_preserves_whitelisted_html(): void
    {
        // Arrange
        Config::set('security.sanitize.enabled', true);
        Config::set('security.sanitize.whitelist_fields', ['description']);
        Config::set('security.sanitize.allowed_tags', '<p><strong>');

        $middleware = new RequestSanitizerMiddleware();
        $request = Request::create('/test', 'POST', [
            'description' => '<p><strong>Bold text</strong></p><script>alert("xss")</script>',
        ]);

        // Act
        $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        // Assert
        $sanitized = $request->input('description');
        $this->assertStringContainsString('<p>', $sanitized);
        $this->assertStringContainsString('<strong>', $sanitized);
        $this->assertStringNotContainsString('<script>', $sanitized);
    }

    public function it_skips_sanitization_when_disabled(): void
    {
        // Arrange
        Config::set('security.sanitize.enabled', false);

        $middleware = new RequestSanitizerMiddleware();
        $original = '<script>alert("test")</script>';
        $request = Request::create('/test', 'POST', [
            'content' => $original,
        ]);

        // Act
        $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        // Assert
        $this->assertEquals($original, $request->input('content'));
    }
}
