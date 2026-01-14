<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Web middleware
        $middleware->web(append: [
            \App\Http\Middleware\LocalizationMiddleware::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\PerformanceMonitoringMiddleware::class,
        ]);
        
        // API middleware
        $middleware->api(append: [
            \App\Http\Middleware\ApiVersionMiddleware::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\RequestSanitizerMiddleware::class,
            \App\Http\Middleware\PerformanceMonitoringMiddleware::class,
        ]);
        
        // Alias middleware for route-level usage
        $middleware->alias([
            'activity.log' => \App\Http\Middleware\ActivityLogMiddleware::class,
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'rate.limit.user' => \App\Http\Middleware\RateLimitByUserMiddleware::class,
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        App\Providers\AppServiceProvider::class,
    ])
    ->create();
