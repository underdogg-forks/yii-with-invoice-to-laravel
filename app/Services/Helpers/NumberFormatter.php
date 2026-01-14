<?php

namespace App\Services\Helpers;

use NumberFormatter as IntlNumberFormatter;

class NumberFormatter
{
    /**
     * Format number with locale awareness.
     */
    public function formatNumber(float $number, int $decimals = 2, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        if (class_exists(IntlNumberFormatter::class)) {
            $formatter = new IntlNumberFormatter($locale, IntlNumberFormatter::DECIMAL);
            $formatter->setAttribute(IntlNumberFormatter::FRACTION_DIGITS, $decimals);
            return $formatter->format($number);
        }

        // Fallback
        return number_format($number, $decimals);
    }

    /**
     * Format currency with locale awareness.
     */
    public function formatCurrency(float $amount, string $currency, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        if (class_exists(IntlNumberFormatter::class)) {
            $formatter = new IntlNumberFormatter($locale, IntlNumberFormatter::CURRENCY);
            return $formatter->formatCurrency($amount, $currency);
        }

        // Fallback to simple format
        return $currency . ' ' . number_format($amount, 2);
    }

    /**
     * Format percentage.
     */
    public function formatPercentage(float $value, int $decimals = 2): string
    {
        return number_format($value, $decimals) . '%';
    }

    /**
     * Format file size.
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        
        return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Format ordinal number.
     */
    public function formatOrdinal(int $number): string
    {
        $suffix = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        }

        return $number . $suffix[$number % 10];
    }

    /**
     * Parse number from formatted string.
     */
    public function parseNumber(string $formatted, ?string $locale = null): float
    {
        // Remove non-numeric characters except decimal point and minus
        $cleaned = preg_replace('/[^0-9.,\-]/', '', $formatted);
        
        // Handle different decimal separators
        if (str_contains($cleaned, ',') && str_contains($cleaned, '.')) {
            // Determine which is the decimal separator
            $commaPos = strrpos($cleaned, ',');
            $dotPos = strrpos($cleaned, '.');
            
            if ($commaPos > $dotPos) {
                // Comma is decimal separator
                $cleaned = str_replace('.', '', $cleaned);
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                // Dot is decimal separator
                $cleaned = str_replace(',', '', $cleaned);
            }
        } elseif (str_contains($cleaned, ',')) {
            // Only comma - check if it's thousands or decimal
            if (substr_count($cleaned, ',') === 1 && strlen(substr($cleaned, strrpos($cleaned, ',') + 1)) <= 2) {
                // Likely decimal separator
                $cleaned = str_replace(',', '.', $cleaned);
            } else {
                // Likely thousands separator
                $cleaned = str_replace(',', '', $cleaned);
            }
        }

        return (float) $cleaned;
    }
}
