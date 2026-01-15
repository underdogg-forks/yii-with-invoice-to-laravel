<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ApiVersionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $version = $this->extractVersion($request);

        if (!$version) {
            $version = Config::get('api.default_version', '1');
        }

        // Validate version
        if (!$this->isValidVersion($version)) {
            return response()->json([
                'error' => 'Invalid API version',
                'message' => "API version '{$version}' is not supported",
            ], 400);
        }

        // Check if version is deprecated
        $deprecationWarning = $this->getDeprecationWarning($version);
        
        // Store version in request for controller access
        $request->attributes->set('api_version', $version);

        $response = $next($request);

        // Add deprecation warning header if needed
        if ($deprecationWarning && Config::get('api.deprecation_warning', true)) {
            $response->headers->set('X-API-Deprecation', $deprecationWarning);
        }

        // Add version header to response
        $response->headers->set('X-API-Version', $version);

        return $response;
    }

    /**
     * Extract version from request.
     */
    protected function extractVersion(Request $request): ?string
    {
        // Check header
        $accept = $request->header('Accept');
        $pattern = Config::get('api.header_pattern', '/application\/vnd\.api\+json;\s*version=(\d+)/');
        
        if ($accept && preg_match($pattern, $accept, $matches)) {
            return $matches[1];
        }

        // Check query parameter as fallback
        return $request->query('version');
    }

    /**
     * Check if version is valid.
     */
    protected function isValidVersion(string $version): bool
    {
        $versions = Config::get('api.versions', []);
        
        return isset($versions[$version]);
    }

    /**
     * Get deprecation warning for version.
     */
    protected function getDeprecationWarning(string $version): ?string
    {
        $versions = Config::get('api.versions', []);
        $versionInfo = $versions[$version] ?? null;

        if (!$versionInfo || $versionInfo['status'] !== 'deprecated') {
            return null;
        }

        $message = "API version {$version} is deprecated";

        if (isset($versionInfo['deprecation_date'])) {
            $message .= " since {$versionInfo['deprecation_date']}";
        }

        if (isset($versionInfo['sunset_date'])) {
            $message .= " and will be removed on {$versionInfo['sunset_date']}";
        }

        return $message;
    }
}
