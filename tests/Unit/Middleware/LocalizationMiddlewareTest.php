<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class LocalizationMiddlewareTest extends TestCase
{
    public function it_sets_locale_from_session(): void
    {
        // Arrange
        Session::put('locale', 'nl');
        
        $middleware = new LocalizationMiddleware();
        $request = Request::create('/test', 'GET');

        // Act
        $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        // Assert
        $this->assertEquals('nl', App::getLocale());
    }

    public function it_sets_locale_from_accept_language_header(): void
    {
        // Arrange
        $middleware = new LocalizationMiddleware();
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept-Language', 'fr,en;q=0.9,nl;q=0.8');

        // Act
        $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        // Assert
        $this->assertEquals('fr', App::getLocale());
    }

    public function it_ignores_unsupported_locales(): void
    {
        // Arrange
        $middleware = new LocalizationMiddleware();
        $request = Request::create('/test', 'GET');
        $request->headers->set('Accept-Language', 'zh,ja;q=0.9'); // Unsupported languages

        // Act
        $middleware->handle($request, function ($req) {
            return new Response('OK');
        });

        // Assert
        $this->assertEquals('en', App::getLocale()); // Falls back to default
    }
}
