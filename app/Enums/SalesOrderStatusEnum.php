<?php

namespace App\Enums;

enum SalesOrderStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    /**
     * Get the color for the status (for UI)
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::CONFIRMED => 'blue',
            self::PROCESSING => 'yellow',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }

    /**
     * Check if order can be edited
     */
    public function canEdit(): bool
    {
        return match($this) {
            self::PENDING => true,
            default => false,
        };
    }

    /**
     * Check if order can be cancelled
     */
    public function canCancel(): bool
    {
        return match($this) {
            self::PENDING, self::CONFIRMED => true,
            default => false,
        };
    }

    /**
     * Get next possible statuses
     */
    public function nextStatuses(): array
    {
        return match($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::COMPLETED, self::CANCELLED],
            default => [],
        };
    }

    /**
     * Get all enum cases as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get cases for select dropdown
     */
    public static function forSelect(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}
