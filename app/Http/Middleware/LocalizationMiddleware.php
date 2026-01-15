<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Supported locales.
     */
    protected array $supportedLocales = ['en', 'nl', 'de', 'fr', 'es'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);

        if ($locale && in_array($locale, $this->supportedLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            
            // Set Carbon locale
            if (class_exists(\Carbon\Carbon::class)) {
                \Carbon\Carbon::setLocale($locale);
            }
        }

        return $next($request);
    }

    /**
     * Determine locale from various sources.
     */
    protected function determineLocale(Request $request): string
    {
        // 1. Check user preference (if authenticated)
        if (Auth::check()) {
            $user = Auth::user();
            if (property_exists($user, 'locale') && $user->locale) {
                return $user->locale;
            }
        }

        // 2. Check session
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 3. Check Accept-Language header
        $browserLocale = $this->parseAcceptLanguage($request);
        if ($browserLocale) {
            return $browserLocale;
        }

        // 4. Use application default
        return config('app.locale', 'en');
    }

    /**
     * Parse Accept-Language header.
     */
    protected function parseAcceptLanguage(Request $request): ?string
    {
        $header = $request->header('Accept-Language');

        if (!$header) {
            return null;
        }

        // Parse language with quality values
        $languages = [];
        foreach (explode(',', $header) as $lang) {
            $parts = explode(';', $lang);
            $code = trim($parts[0]);
            $quality = 1.0;

            if (isset($parts[1]) && str_contains($parts[1], 'q=')) {
                $quality = (float) str_replace('q=', '', trim($parts[1]));
            }

            // Extract base language code (e.g., 'en' from 'en-US')
            $baseCode = strtolower(substr($code, 0, 2));
            
            if (in_array($baseCode, $this->supportedLocales)) {
                $languages[$baseCode] = $quality;
            }
        }

        if (empty($languages)) {
            return null;
        }

        // Sort by quality value
        arsort($languages);

        // Return highest quality supported language
        return array_key_first($languages);
    }
}
