<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Guard clauses
        if (!Config::get('tenant.enabled', false)) {
            return $next($request);
        }

        $tenant = $this->identifyTenant($request);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        if (!$tenant->is_active) {
            return response()->json(['error' => 'Tenant is inactive'], 403);
        }

        // Set tenant context
        $this->setTenantContext($tenant);

        return $next($request);
    }

    /**
     * Identify tenant from request.
     */
    protected function identifyTenant(Request $request): ?Tenant
    {
        $mode = Config::get('tenant.mode', 'subdomain');

        return match($mode) {
            'subdomain' => $this->identifyBySubdomain($request),
            'domain' => $this->identifyByDomain($request),
            'path' => $this->identifyByPath($request),
            default => null,
        };
    }

    /**
     * Identify tenant by subdomain.
     */
    protected function identifyBySubdomain(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $centralDomain = Config::get('tenant.central_domain');

        // Extract subdomain
        $subdomain = str_replace('.' . $centralDomain, '', $host);

        if ($subdomain === $centralDomain || $subdomain === 'www') {
            return null;
        }

        return $this->getCachedTenant('subdomain', $subdomain);
    }

    /**
     * Identify tenant by domain.
     */
    protected function identifyByDomain(Request $request): ?Tenant
    {
        $domain = $request->getHost();
        
        return $this->getCachedTenant('domain', $domain);
    }

    /**
     * Identify tenant by path.
     */
    protected function identifyByPath(Request $request): ?Tenant
    {
        $segments = $request->segments();
        
        if (empty($segments)) {
            return null;
        }

        $subdomain = $segments[0];
        
        return $this->getCachedTenant('subdomain', $subdomain);
    }

    /**
     * Get cached tenant.
     */
    protected function getCachedTenant(string $column, string $value): ?Tenant
    {
        $cacheKey = "tenant:{$column}:{$value}";
        $cacheTtl = Config::get('tenant.cache_ttl', 3600);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($column, $value) {
            return Tenant::where($column, $value)->first();
        });
    }

    /**
     * Set tenant context.
     */
    protected function setTenantContext(Tenant $tenant): void
    {
        // Store tenant in config for access throughout the application
        Config::set('tenant.current', $tenant);

        // Store tenant ID in the app container
        app()->instance('tenant', $tenant);

        // If using separate databases, switch connection
        if (Config::get('tenant.database.separate', false) && $tenant->database) {
            Config::set('database.connections.tenant', [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $tenant->database,
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
            ]);
            
            Config::set('database.default', 'tenant');
        }
    }
}
