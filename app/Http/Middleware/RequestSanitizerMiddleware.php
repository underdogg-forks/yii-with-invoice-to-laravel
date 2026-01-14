<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class RequestSanitizerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Guard clause
        if (!Config::get('security.sanitize.enabled', true)) {
            return $next($request);
        }

        $this->sanitizeInput($request);

        return $next($request);
    }

    /**
     * Sanitize request input.
     */
    protected function sanitizeInput(Request $request): void
    {
        $input = $request->all();
        $sanitized = $this->sanitizeArray($input);
        
        $request->merge($sanitized);
    }

    /**
     * Recursively sanitize array.
     */
    protected function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($key, $value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize string value.
     */
    protected function sanitizeString(string $key, string $value): string
    {
        // Trim whitespace
        $value = trim($value);

        // Remove null bytes
        $value = str_replace("\0", '', $value);

        // Check if field is whitelisted for HTML
        $whitelist = Config::get('security.sanitize.whitelist_fields', []);
        
        if (in_array($key, $whitelist)) {
            return $this->sanitizeHtml($value);
        }

        // Strip all HTML tags for non-whitelisted fields
        return strip_tags($value);
    }

    /**
     * Sanitize HTML with allowed tags.
     */
    protected function sanitizeHtml(string $value): string
    {
        $allowedTags = Config::get('security.sanitize.allowed_tags', '');
        
        return strip_tags($value, $allowedTags);
    }
}
