<?php

namespace App\Services\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class CurrencyConverter
{
    /**
     * Convert amount from one currency to another.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        // Guard clauses
        if ($from === $to) {
            return $amount;
        }

        $rate = $this->getRate($from, $to);

        return round($amount * $rate, 2);
    }

    /**
     * Get exchange rate between two currencies.
     */
    public function getRate(string $from, string $to): float
    {
        $cacheKey = "exchange_rate:{$from}:{$to}";
        $cacheTtl = Config::get('currency.cache_ttl', 86400);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($from, $to) {
            return $this->fetchRate($from, $to);
        });
    }

    /**
     * Fetch exchange rate from API.
     */
    protected function fetchRate(string $from, string $to): float
    {
        $provider = Config::get('currency.api_provider', 'fixer');

        return match($provider) {
            'fixer' => $this->fetchFromFixer($from, $to),
            'openexchangerates' => $this->fetchFromOpenExchangeRates($from, $to),
            default => throw new \Exception("Unsupported currency provider: {$provider}"),
        };
    }

    /**
     * Fetch rate from Fixer.io API.
     */
    protected function fetchFromFixer(string $from, string $to): float
    {
        $apiKey = Config::get('currency.api_key');

        if (!$apiKey) {
            throw new \Exception('Currency API key not configured');
        }

        $response = Http::get("https://api.fixer.io/latest", [
            'access_key' => $apiKey,
            'base' => $from,
            'symbols' => $to,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch exchange rate');
        }

        $data = $response->json();

        if (!isset($data['rates'][$to])) {
            throw new \Exception("Exchange rate not found for {$from} to {$to}");
        }

        return (float) $data['rates'][$to];
    }

    /**
     * Fetch rate from Open Exchange Rates API.
     */
    protected function fetchFromOpenExchangeRates(string $from, string $to): float
    {
        $apiKey = Config::get('currency.api_key');
        $base = Config::get('currency.base_currency', 'USD');

        if (!$apiKey) {
            throw new \Exception('Currency API key not configured');
        }

        $response = Http::get("https://openexchangerates.org/api/latest.json", [
            'app_id' => $apiKey,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch exchange rate');
        }

        $data = $response->json();
        $rates = $data['rates'] ?? [];

        if (!isset($rates[$from]) || !isset($rates[$to])) {
            throw new \Exception("Exchange rate not found");
        }

        // Convert from base currency
        return $rates[$to] / $rates[$from];
    }

    /**
     * Get available currencies.
     */
    public function getAvailableCurrencies(): array
    {
        return Config::get('currency.supported_currencies', []);
    }

    /**
     * Format money with currency symbol.
     */
    public function formatMoney(float $amount, string $currency, ?string $locale = null): string
    {
        $format = Config::get("currency.format.{$currency}");

        if (!$format) {
            return number_format($amount, 2);
        }

        $symbol = $format['symbol'];
        $decimals = $format['decimals'];
        $position = $format['position'] ?? 'before';

        $formatted = number_format($amount, $decimals);

        return $position === 'before'
            ? "{$symbol}{$formatted}"
            : "{$formatted} {$symbol}";
    }
}
