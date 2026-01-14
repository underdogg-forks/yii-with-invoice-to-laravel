<?php

namespace App\Http\Middleware;

use App\Models\PerformanceMetric;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoringMiddleware
{
    /**
     * Query count at start.
     */
    protected int $queryCountStart = 0;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Enable query log
        DB::enableQueryLog();
        $this->queryCountStart = count(DB::getQueryLog());

        $response = $next($request);

        $executionTime = (microtime(true) - $startTime) * 1000; // milliseconds
        $memoryUsage = memory_get_usage() - $startMemory;
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries) - $this->queryCountStart;
        $queryTime = $this->calculateQueryTime($queries);

        $this->logMetrics(
            $request,
            $executionTime,
            $queryCount,
            $queryTime,
            $memoryUsage
        );

        // Add performance headers
        $response->headers->set('X-Response-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Query-Count', $queryCount);

        return $response;
    }

    /**
     * Calculate total query execution time.
     */
    protected function calculateQueryTime(array $queries): float
    {
        $total = 0;
        
        foreach ($queries as $query) {
            $total += $query['time'] ?? 0;
        }

        return $total;
    }

    /**
     * Log performance metrics.
     */
    protected function logMetrics(
        Request $request,
        float $executionTime,
        int $queryCount,
        float $queryTime,
        int $memoryUsage
    ): void {
        // Check if slow request (configurable threshold)
        $slowThreshold = Config::get('performance.slow_threshold', 1000); // 1 second
        
        if ($executionTime > $slowThreshold) {
            logger()->warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime,
                'query_count' => $queryCount,
            ]);
        }

        // Store metrics (optional - can be disabled for performance)
        if (Config::get('performance.store_metrics', false)) {
            try {
                PerformanceMetric::create([
                    'url' => substr($request->path(), 0, 255),
                    'method' => $request->method(),
                    'execution_time' => $executionTime,
                    'query_count' => $queryCount,
                    'query_time' => $queryTime,
                    'memory_usage' => $memoryUsage,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Fail silently
                logger()->error('Failed to store performance metrics: ' . $e->getMessage());
            }
        }
    }
}
