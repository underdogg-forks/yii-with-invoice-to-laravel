<?php

namespace App\Services\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Add business days to a date (excluding weekends).
     */
    public function addBusinessDays(Carbon $date, int $days): Carbon
    {
        $result = $date->copy();
        $daysAdded = 0;

        while ($daysAdded < $days) {
            $result->addDay();
            
            if ($this->isBusinessDay($result)) {
                $daysAdded++;
            }
        }

        return $result;
    }

    /**
     * Calculate business days between two dates.
     */
    public function getBusinessDaysBetween(Carbon $start, Carbon $end): int
    {
        $businessDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($this->isBusinessDay($current)) {
                $businessDays++;
            }
            $current->addDay();
        }

        return $businessDays;
    }

    /**
     * Check if date is a business day.
     */
    public function isBusinessDay(Carbon $date): bool
    {
        // Check if weekend
        if ($date->isWeekend()) {
            return false;
        }

        // Check if holiday (can be extended with holiday database)
        return !$this->isHoliday($date);
    }

    /**
     * Check if date is a holiday.
     */
    protected function isHoliday(Carbon $date): bool
    {
        // This can be extended to check against a database of holidays
        // For now, just return false
        return false;
    }

    /**
     * Parse flexible date input.
     */
    public function parseFlexibleDate(string $input): Carbon
    {
        // Handle relative dates
        $input = strtolower(trim($input));

        return match($input) {
            'today' => Carbon::today(),
            'tomorrow' => Carbon::tomorrow(),
            'yesterday' => Carbon::yesterday(),
            default => $this->parseDate($input),
        };
    }

    /**
     * Parse date from string.
     */
    protected function parseDate(string $input): Carbon
    {
        // Try common formats
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'd-m-Y',
            'm-d-Y',
            'Y/m/d',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $input);
            } catch (\Exception $e) {
                continue;
            }
        }

        // Fallback to Carbon's parser
        return Carbon::parse($input);
    }

    /**
     * Format date relative to now.
     */
    public function formatRelative(Carbon $date): string
    {
        return $date->diffForHumans();
    }

    /**
     * Get date range.
     */
    public function getDateRange(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        return $dates;
    }
}
