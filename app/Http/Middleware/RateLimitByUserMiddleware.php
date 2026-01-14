<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class RateLimitByUserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveKey($request);
        $limits = $this->getLimits($request);

        $attempts = Cache::get($key, 0);

        if ($attempts >= $limits['requests']) {
            $retryAfter = $this->getRetryAfter($key);
            
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $retryAfter,
            ], 429)
            ->header('Retry-After', $retryAfter)
            ->header('X-RateLimit-Limit', $limits['requests'])
            ->header('X-RateLimit-Remaining', 0);
        }

        // Increment attempts
        $this->incrementAttempts($key, $limits['period']);

        $response = $next($request);

        // Add rate limit headers
        $remaining = max(0, $limits['requests'] - ($attempts + 1));
        
        return $response
            ->header('X-RateLimit-Limit', $limits['requests'])
            ->header('X-RateLimit-Remaining', $remaining);
    }

    /**
     * Resolve cache key for rate limiting.
     */
    protected function resolveKey(Request $request): string
    {
        $prefix = Config::get('rate-limit.prefix', 'rate_limit:');
        
        if (Auth::check()) {
            return $prefix . 'user:' . Auth::id();
        }

        return $prefix . 'ip:' . $request->ip();
    }

    /**
     * Get rate limits for current user.
     */
    protected function getLimits(Request $request): array
    {
        // API routes have different limits
        if ($request->is('api/*')) {
            return Config::get('rate-limit.api');
        }

        if (!Auth::check()) {
            return Config::get('rate-limit.guest');
        }

        $user = Auth::user();

        // Check if user is admin
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return Config::get('rate-limit.admin');
        }

        return Config::get('rate-limit.authenticated');
    }

    /**
     * Increment request attempts.
     */
    protected function incrementAttempts(string $key, int $period): void
    {
        Cache::add($key, 0, $period);
        Cache::increment($key);
    }

    /**
     * Get retry after time in seconds.
     */
    protected function getRetryAfter(string $key): int
    {
        // Get TTL from cache
        $ttl = Cache::get($key . ':ttl');
        
        return $ttl ?? 60;
    }
}
