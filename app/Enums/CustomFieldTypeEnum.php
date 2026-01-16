<?php

namespace App\Enums;

enum CustomFieldTypeEnum: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case CHECKBOX = 'checkbox';
    case SELECT = 'select';
    case DATE = 'date';
    case NUMBER = 'number';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::TEXTAREA => 'Textarea',
            self::CHECKBOX => 'Checkbox',
            self::SELECT => 'Select',
            self::DATE => 'Date',
            self::NUMBER => 'Number',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TEXT => 'primary',
            self::TEXTAREA => 'success',
            self::CHECKBOX => 'warning',
            self::SELECT => 'info',
            self::DATE => 'gray',
            self::NUMBER => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $enum) => [$enum->value => $enum->label()])
            ->toArray();
    }
}
