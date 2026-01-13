<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ClientPeppolRepository;
use App\Repositories\PaymentPeppolRepository;
use App\Repositories\UnitPeppolRepository;
use App\Repositories\UserRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\InvoiceItemRepository;
use App\Repositories\InvoiceAmountRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TaxRateRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\SalesOrderRepository;
use App\Services\ClientPeppolService;
use App\Services\PaymentPeppolService;
use App\Services\UnitPeppolService;
use App\Services\UserService;
use App\Services\AuthService;
use App\Services\InvoiceService;
use App\Services\InvoiceItemService;
use App\Services\InvoiceAmountService;
use App\Services\ProductService;
use App\Services\TaxRateService;
use App\Services\ClientService;
use App\Services\CustomFieldService;
use App\Services\QuoteService;
use App\Services\SalesOrderService;
use PragmaRX\Google2FA\Google2FA;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repositories (alphabetically sorted)
        $this->app->singleton(ClientPeppolRepository::class);
        $this->app->singleton(ClientRepository::class);
        $this->app->singleton(CustomFieldRepository::class);
        $this->app->singleton(InvoiceAmountRepository::class);
        $this->app->singleton(InvoiceItemRepository::class);
        $this->app->singleton(InvoiceRepository::class);
        $this->app->singleton(PaymentPeppolRepository::class);
        $this->app->singleton(ProductRepository::class);
        $this->app->singleton(QuoteRepository::class);
        $this->app->singleton(SalesOrderRepository::class);
        $this->app->singleton(TaxRateRepository::class);
        $this->app->singleton(UnitPeppolRepository::class);
        $this->app->singleton(UserRepository::class);

        // Register Google2FA
        $this->app->singleton(Google2FA::class, function () {
            return new Google2FA();
        });

        // Register Services (alphabetically sorted)
        $this->app->singleton(AuthService::class);
        $this->app->singleton(ClientPeppolService::class);
        $this->app->singleton(ClientService::class);
        $this->app->singleton(CustomFieldService::class);
        $this->app->singleton(InvoiceAmountService::class);
        $this->app->singleton(InvoiceItemService::class);
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(PaymentPeppolService::class);
        $this->app->singleton(ProductService::class);
        $this->app->singleton(QuoteService::class);
        $this->app->singleton(SalesOrderService::class);
        $this->app->singleton(TaxRateService::class);
        $this->app->singleton(UnitPeppolService::class);
        $this->app->singleton(UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
