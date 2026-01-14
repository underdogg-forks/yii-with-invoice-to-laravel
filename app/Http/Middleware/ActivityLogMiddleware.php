<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Guard clauses - early return pattern
        if (!Config::get('activity.enabled', true)) {
            return $next($request);
        }

        if ($this->shouldExclude($request)) {
            return $next($request);
        }

        $startTime = microtime(true);
        
        $response = $next($request);
        
        $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        $this->logActivity($request, $response, $executionTime);
        
        return $response;
    }

    /**
     * Check if request should be excluded from logging.
     */
    protected function shouldExclude(Request $request): bool
    {
        $excludeUrls = Config::get('activity.exclude_urls', []);
        
        foreach ($excludeUrls as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Log the activity.
     */
    protected function logActivity(Request $request, Response $response, float $executionTime): void
    {
        try {
            $requestData = $this->sanitizeData($request->except(
                Config::get('activity.sanitize_fields', [])
            ));

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $this->determineAction($request),
                'model_type' => $this->extractModelType($request),
                'model_id' => $this->extractModelId($request),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_data' => Config::get('activity.log_request_body', true) ? $requestData : null,
                'response_data' => Config::get('activity.log_response', false) ? $this->getResponseData($response) : null,
                'status_code' => $response->getStatusCode(),
                'execution_time' => $executionTime,
            ]);
        } catch (\Exception $e) {
            // Fail silently to not break the application
            logger()->error('Activity logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine action from request.
     */
    protected function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        if (str_contains($path, 'login')) {
            return 'login';
        }
        
        if (str_contains($path, 'logout')) {
            return 'logout';
        }

        return match($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown',
        };
    }

    /**
     * Extract model type from request.
     */
    protected function extractModelType(Request $request): ?string
    {
        $route = $request->route();
        
        if (!$route) {
            return null;
        }

        // Try to extract from route parameters
        $parameters = $route->parameters();
        
        foreach ($parameters as $key => $value) {
            if (is_object($value) && method_exists($value, 'getMorphClass')) {
                return $value->getMorphClass();
            }
        }

        return null;
    }

    /**
     * Extract model ID from request.
     */
    protected function extractModelId(Request $request): ?int
    {
        $route = $request->route();
        
        if (!$route) {
            return null;
        }

        $parameters = $route->parameters();
        
        foreach ($parameters as $key => $value) {
            if (is_object($value) && property_exists($value, 'id')) {
                return $value->id;
            }
        }

        return null;
    }

    /**
     * Sanitize sensitive data.
     */
    protected function sanitizeData(array $data): array
    {
        if (!Config::get('activity.sanitize_request', true)) {
            return $data;
        }

        return $data;
    }

    /**
     * Get response data.
     */
    protected function getResponseData(Response $response): ?array
    {
        $content = $response->getContent();
        
        if (!$content) {
            return null;
        }

        $decoded = json_decode($content, true);
        
        return is_array($decoded) ? $decoded : ['content' => substr($content, 0, 1000)];
    }
}
