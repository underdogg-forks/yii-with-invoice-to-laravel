<?php

namespace App\Enums;

enum TemplateTypeEnum: string
{
    case INVOICE = 'invoice';
    case QUOTE = 'quote';
    case SALES_ORDER = 'sales_order';
    case EMAIL = 'email';
    case PDF = 'pdf';

    /**
     * Get the label for the template type
     */
    public function label(): string
    {
        return match($this) {
            self::INVOICE => 'Invoice Template',
            self::QUOTE => 'Quote Template',
            self::SALES_ORDER => 'Sales Order Template',
            self::EMAIL => 'Email Template',
            self::PDF => 'PDF Template',
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
