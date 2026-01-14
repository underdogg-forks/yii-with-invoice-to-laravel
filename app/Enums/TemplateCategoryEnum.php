<?php

namespace App\Enums;

enum TemplateCategoryEnum: string
{
    case STANDARD = 'standard';
    case CUSTOM = 'custom';
    case SYSTEM = 'system';
    case ARCHIVED = 'archived';

    /**
     * Get the label for the template category
     */
    public function label(): string
    {
        return match($this) {
            self::STANDARD => 'Standard',
            self::CUSTOM => 'Custom',
            self::SYSTEM => 'System',
            self::ARCHIVED => 'Archived',
        };
    }

    /**
     * Check if template can be modified
     */
    public function canModify(): bool
    {
        return match($this) {
            self::CUSTOM => true,
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
