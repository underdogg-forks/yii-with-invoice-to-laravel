<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'health:check';

    /**
     * The console command description.
     */
    protected $description = 'Check system health';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Running health checks...');

        $allHealthy = true;

        // Check database
        $allHealthy = $this->checkDatabase() && $allHealthy;

        // Check cache
        $allHealthy = $this->checkCache() && $allHealthy;

        // Check storage
        $allHealthy = $this->checkStorage() && $allHealthy;

        // Check disk space
        $allHealthy = $this->checkDiskSpace() && $allHealthy;

        // Check external APIs
        $allHealthy = $this->checkExternalApis() && $allHealthy;

        if ($allHealthy) {
            $this->info('✓ All health checks passed');
            return self::SUCCESS;
        } else {
            $this->error('✗ Some health checks failed');
            return self::FAILURE;
        }
    }

    /**
     * Check database connectivity.
     */
    protected function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database: Connected');
            return true;
        } catch (\Exception $e) {
            $this->error('✗ Database: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check cache connectivity.
     */
    protected function checkCache(): bool
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);

            if ($value === 'test') {
                $this->info('✓ Cache: Working');
                return true;
            } else {
                $this->error('✗ Cache: Failed to retrieve test value');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('✗ Cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check storage.
     */
    protected function checkStorage(): bool
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::put($testFile, 'test');
            $exists = Storage::exists($testFile);
            Storage::delete($testFile);

            if ($exists) {
                $this->info('✓ Storage: Working');
                return true;
            } else {
                $this->error('✗ Storage: Failed to write test file');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('✗ Storage: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check disk space.
     */
    protected function checkDiskSpace(): bool
    {
        $storagePath = storage_path();
        $freeSpace = disk_free_space($storagePath);
        $totalSpace = disk_total_space($storagePath);
        $usedPercent = (1 - ($freeSpace / $totalSpace)) * 100;

        $freeGB = round($freeSpace / 1024 / 1024 / 1024, 2);

        if ($usedPercent > 90) {
            $this->error("✗ Disk Space: Only {$freeGB} GB free ({$usedPercent}% used)");
            return false;
        } else {
            $this->info("✓ Disk Space: {$freeGB} GB free");
            return true;
        }
    }

    /**
     * Check external APIs.
     */
    protected function checkExternalApis(): bool
    {
        $allHealthy = true;

        // Check currency API if configured
        if (config('currency.api_key')) {
            try {
                $response = Http::timeout(5)->get('https://api.fixer.io/latest', [
                    'access_key' => config('currency.api_key'),
                ]);

                if ($response->successful()) {
                    $this->info('✓ Currency API: Accessible');
                } else {
                    $this->error('✗ Currency API: Not accessible');
                    $allHealthy = false;
                }
            } catch (\Exception $e) {
                $this->error('✗ Currency API: ' . $e->getMessage());
                $allHealthy = false;
            }
        }

        return $allHealthy;
    }
}
