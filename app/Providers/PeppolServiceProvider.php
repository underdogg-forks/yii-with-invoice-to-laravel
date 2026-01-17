<?php

namespace App\Providers;

use App\Contracts\ApiClientInterface;
use App\Services\Http\ApiClient;
use App\Services\Http\Decorators\HttpClientExceptionHandler;
use App\Services\Http\Decorators\RequestLogger;
use Illuminate\Support\ServiceProvider;

class PeppolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ApiClientInterface::class, function ($app) {
            // Build the decorated client: ApiClient -> RequestLogger -> HttpClientExceptionHandler
            $baseClient = new ApiClient();
            $loggedClient = new RequestLogger($baseClient);
            $handledClient = new HttpClientExceptionHandler($loggedClient);
            
            return $handledClient;
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/peppol.php' => config_path('peppol.php'),
        ], 'config');
    }
}
