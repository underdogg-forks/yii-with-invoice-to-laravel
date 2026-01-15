<?php

namespace App\Enums;

enum TemplateVariableTypeEnum: string
{
    case STRING = 'string';
    case NUMBER = 'number';
    case DATE = 'date';
    case CURRENCY = 'currency';
    case BOOLEAN = 'boolean';
    case IMAGE = 'image';

    /**
     * Get the label for the variable type
     */
    public function label(): string
    {
        return match($this) {
            self::STRING => 'Text',
            self::NUMBER => 'Number',
            self::DATE => 'Date',
            self::CURRENCY => 'Currency',
            self::BOOLEAN => 'Yes/No',
            self::IMAGE => 'Image',
        };
    }

    /**
     * Get validation rules for the type
     */
    public function validationRules(): string
    {
        return match($this) {
            self::STRING => 'string',
            self::NUMBER => 'numeric',
            self::DATE => 'date',
            self::CURRENCY => 'numeric|min:0',
            self::BOOLEAN => 'boolean',
            self::IMAGE => 'image|max:2048',
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
