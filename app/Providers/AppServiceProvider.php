<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ClientPeppolRepository;
use App\Repositories\PaymentPeppolRepository;
use App\Repositories\UnitPeppolRepository;
use App\Repositories\UserRepository;
use App\Services\ClientPeppolService;
use App\Services\PaymentPeppolService;
use App\Services\UnitPeppolService;
use App\Services\UserService;
use App\Services\AuthService;
use PragmaRX\Google2FA\Google2FA;

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
        $this->app->singleton(UserRepository::class);

        // Register Google2FA
        $this->app->singleton(Google2FA::class, function () {
            return new Google2FA();
        });

        // Register Services
        $this->app->singleton(ClientPeppolService::class);
        $this->app->singleton(PaymentPeppolService::class);
        $this->app->singleton(UnitPeppolService::class);
        $this->app->singleton(UserService::class);
        $this->app->singleton(AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
