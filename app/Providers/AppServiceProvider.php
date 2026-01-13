<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ClientPeppolRepository;
use App\Repositories\PaymentPeppolRepository;
use App\Repositories\UnitPeppolRepository;
use App\Services\ClientPeppolService;
use App\Services\PaymentPeppolService;
use App\Services\UnitPeppolService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->singleton(ClientPeppolRepository::class);
        $this->app->singleton(PaymentPeppolRepository::class);
        $this->app->singleton(UnitPeppolRepository::class);

        // Register Services
        $this->app->singleton(ClientPeppolService::class, function ($app) {
            return new ClientPeppolService($app->make(ClientPeppolRepository::class));
        });

        $this->app->singleton(PaymentPeppolService::class, function ($app) {
            return new PaymentPeppolService($app->make(PaymentPeppolRepository::class));
        });

        $this->app->singleton(UnitPeppolService::class, function ($app) {
            return new UnitPeppolService($app->make(UnitPeppolRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
