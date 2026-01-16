<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class CurrencyInput extends TextInput
{
    protected string $view = 'filament.forms.components.currency-input';

    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix('$')
            ->numeric()
            ->step('0.01')
            ->minValue(0)
            ->maxValue(999999999.99)
            ->inputMode('decimal')
            ->extraInputAttributes([
                'class' => 'text-right',
            ])
            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 2, '.', '') : null)
            ->dehydrateStateUsing(fn ($state) => $state ? (float) str_replace(',', '', $state) : null);
    }

    public function currency(string $currency = 'USD', string $locale = 'en_US'): static
    {
        $this->prefix(match ($currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            default => '$',
        });

        return $this;
    }
}
