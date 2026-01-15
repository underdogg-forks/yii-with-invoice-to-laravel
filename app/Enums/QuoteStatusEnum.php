<?php

namespace App\Enums;

enum QuoteStatusEnum: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case VIEWED = 'viewed';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    /**
     * Get the label for the status
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::VIEWED => 'Viewed',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::EXPIRED => 'Expired',
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
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::EXPIRED => 'orange',
        };
    }

    /**
     * Check if quote can be edited
     */
    public function canEdit(): bool
    {
        return match($this) {
            self::DRAFT => true,
            default => false,
        };
    }

    /**
     * Check if quote can be converted to invoice
     */
    public function canConvertToInvoice(): bool
    {
        return $this === self::APPROVED;
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
