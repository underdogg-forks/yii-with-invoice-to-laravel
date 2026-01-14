<?php

namespace App\Enums;

enum InvoiceStatusEnum: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case VIEWED = 'viewed';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case OVERDUE = 'overdue';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::VIEWED => 'Viewed',
            self::PAID => 'Paid',
            self::CANCELLED => 'Cancelled',
            self::OVERDUE => 'Overdue',
        };
    }

    /**
     * Get the color for the status (for UI)
     */
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::VIEWED => 'purple',
            self::PAID => 'green',
            self::CANCELLED => 'red',
            self::OVERDUE => 'orange',
        };
    }

    /**
     * Check if invoice can be edited
     */
    public function canEdit(): bool
    {
        return match($this) {
            self::DRAFT => true,
            default => false,
        };
    }

    /**
     * Check if invoice can be sent
     */
    public function canSend(): bool
    {
        return match($this) {
            self::DRAFT => true,
            default => false,
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
