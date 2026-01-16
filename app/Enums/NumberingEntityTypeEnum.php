<?php

namespace App\Enums;

enum NumberingEntityTypeEnum: string
{
    case INVOICE = 'invoice';
    case QUOTE = 'quote';
    case CLIENT = 'client';
    case PROJECT = 'project';
    case TASK = 'task';

    public function label(): string
    {
        return match ($this) {
            self::INVOICE => 'Invoice',
            self::QUOTE => 'Quote',
            self::CLIENT => 'Client',
            self::PROJECT => 'Project',
            self::TASK => 'Task',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $enum) => [$enum->value => $enum->label()])
            ->toArray();
    }
}
