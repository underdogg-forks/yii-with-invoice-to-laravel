<?php

namespace App\Enums;

enum AddressTypeEnum: string
{
    case POSTAL = 'postal';
    case VISITING = 'visiting';
    case DELIVERY = 'delivery';
    case BILLING = 'billing';
    case SHIPPING = 'shipping';

    /**
     * Get the label for the address type
     */
    public function label(): string
    {
        return match($this) {
            self::POSTAL => 'Postal Address',
            self::VISITING => 'Visiting Address',
            self::DELIVERY => 'Delivery Address',
            self::BILLING => 'Billing Address',
            self::SHIPPING => 'Shipping Address',
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
