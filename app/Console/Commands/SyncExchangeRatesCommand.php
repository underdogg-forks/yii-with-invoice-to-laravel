<?php

namespace App\Console\Commands;

use App\Services\Helpers\CurrencyConverter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SyncExchangeRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'currency:sync 
                            {--force : Force refresh even if cached}';

    /**
     * The console command description.
     */
    protected $description = 'Sync currency exchange rates from API';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyConverter $converter): int
    {
        $this->info('Syncing exchange rates...');

        $baseCurrency = Config::get('currency.base_currency', 'EUR');
        $currencies = Config::get('currency.supported_currencies', []);

        if (empty($currencies)) {
            $this->error('No currencies configured');
            return self::FAILURE;
        }

        $force = $this->option('force');
        $successCount = 0;
        $failCount = 0;

        foreach ($currencies as $currency) {
            if ($currency === $baseCurrency) {
                continue;
            }

            try {
                // Clear cache if force option is used
                if ($force) {
                    $cacheKey = "exchange_rate:{$baseCurrency}:{$currency}";
                    Cache::forget($cacheKey);
                }

                // Fetch rate (will be cached)
                $rate = $converter->getRate($baseCurrency, $currency);
                
                $this->info("✓ {$baseCurrency} → {$currency}: {$rate}");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("✗ {$baseCurrency} → {$currency}: {$e->getMessage()}");
                $failCount++;
            }
        }

        $this->newLine();
        $this->info("Synced {$successCount} rates successfully");
        
        if ($failCount > 0) {
            $this->error("Failed to sync {$failCount} rates");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
