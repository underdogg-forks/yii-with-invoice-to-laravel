<?php

namespace App\Enums;

enum ReportTypeEnum: string
{
    case PROFIT = 'profit';
    case SALES = 'sales';
    case INVENTORY = 'inventory';
    case TAX = 'tax';
    case CLIENT = 'client';
    case PRODUCT = 'product';

    /**
     * Get the label for the report type
     */
    public function label(): string
    {
        return match($this) {
            self::PROFIT => 'Profit Report',
            self::SALES => 'Sales Report',
            self::INVENTORY => 'Inventory Report',
            self::TAX => 'Tax Report',
            self::CLIENT => 'Client Report',
            self::PRODUCT => 'Product Report',
        };
    }

    /**
     * Get the description for the report type
     */
    public function description(): string
    {
        return match($this) {
            self::PROFIT => 'Detailed profit and loss analysis',
            self::SALES => 'Sales performance and trends',
            self::INVENTORY => 'Stock levels and movements',
            self::TAX => 'Tax calculations and summaries',
            self::CLIENT => 'Client activity and revenue',
            self::PRODUCT => 'Product performance metrics',
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
