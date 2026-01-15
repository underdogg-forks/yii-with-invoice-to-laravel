<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class CacheWarmupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:warmup';

    /**
     * The console command description.
     */
    protected $description = 'Warm up application cache';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Warming up cache...');

        // Cache configuration
        $this->cacheConfiguration();

        // Cache routes
        $this->cacheRoutes();

        // Cache views
        $this->cacheViews();

        // Cache common queries (can be extended)
        $this->cacheCommonQueries();

        $this->info('Cache warmup completed!');

        return self::SUCCESS;
    }

    /**
     * Cache configuration.
     */
    protected function cacheConfiguration(): void
    {
        $this->info('Caching configuration...');
        Artisan::call('config:cache');
        $this->info('Configuration cached');
    }

    /**
     * Cache routes.
     */
    protected function cacheRoutes(): void
    {
        $this->info('Caching routes...');
        Artisan::call('route:cache');
        $this->info('Routes cached');
    }

    /**
     * Cache views.
     */
    protected function cacheViews(): void
    {
        $this->info('Caching views...');
        Artisan::call('view:cache');
        $this->info('Views cached');
    }

    /**
     * Cache common queries.
     */
    protected function cacheCommonQueries(): void
    {
        $this->info('Caching common queries...');

        // Cache application settings
        Cache::remember('app_settings', 3600, function () {
            // This would fetch settings from database
            return [];
        });

        // Cache available currencies
        Cache::remember('available_currencies', 86400, function () {
            return config('currency.supported_currencies', []);
        });

        $this->info('Common queries cached');
    }
}
