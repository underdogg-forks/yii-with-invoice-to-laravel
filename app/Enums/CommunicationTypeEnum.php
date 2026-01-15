<?php

namespace App\Enums;

enum CommunicationTypeEnum: string
{
    case PHONE = 'phone';
    case MOBILE = 'mobile';
    case FAX = 'fax';
    case EMAIL = 'email';
    case WEBSITE = 'website';
    case SKYPE = 'skype';
    case WHATSAPP = 'whatsapp';

    /**
     * Get the label for the communication type
     */
    public function label(): string
    {
        return match($this) {
            self::PHONE => 'Phone',
            self::MOBILE => 'Mobile',
            self::FAX => 'Fax',
            self::EMAIL => 'Email',
            self::WEBSITE => 'Website',
            self::SKYPE => 'Skype',
            self::WHATSAPP => 'WhatsApp',
        };
    }

    /**
     * Get validation rules for the type
     */
    public function validationRules(): string
    {
        return match($this) {
            self::EMAIL => 'email',
            self::WEBSITE => 'url',
            default => 'string',
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
