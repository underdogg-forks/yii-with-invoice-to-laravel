<?php

namespace App\Filament\Forms\Components;

use App\Models\TaxRate;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class TaxRateSelector extends Select
{
    protected string $view = 'filament.forms.components.tax-rate-selector';

    protected function setUp(): void
    {
        parent::setUp();

        $this->relationship('taxRate', 'tax_rate_name')
            ->searchable()
            ->preload()
            ->live()
            ->getOptionLabelFromRecordUsing(fn (TaxRate $record) => "{$record->tax_rate_name} ({$record->tax_rate_percent}%)")
            ->createOptionForm([
                \Filament\Forms\Components\TextInput::make('tax_rate_name')
                    ->required()
                    ->maxLength(50),
                \Filament\Forms\Components\TextInput::make('tax_rate_percent')
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01),
                \Filament\Forms\Components\Toggle::make('tax_rate_is_default')
                    ->label('Set as Default')
                    ->default(false),
            ])
            ->helperText(fn ($state) => $this->getTaxCalculationPreview($state));
    }

    protected function getTaxCalculationPreview(?int $taxRateId): ?string
    {
        if (!$taxRateId) {
            return null;
        }

        $taxRate = TaxRate::find($taxRateId);
        if (!$taxRate) {
            return null;
        }

        // Get the amount from the form context if available
        $amount = $this->getLivewire()->data['item_price'] ?? 
                  $this->getLivewire()->data['product_price'] ?? 
                  100; // Default to 100 for preview

        $taxAmount = $amount * ($taxRate->tax_rate_percent / 100);
        $total = $amount + $taxAmount;

        return sprintf(
            'Preview: $%.2f + $%.2f (%.2f%%) = $%.2f',
            $amount,
            $taxAmount,
            $taxRate->tax_rate_percent,
            $total
        );
    }

    public function withCalculationPreview(bool $condition = true): static
    {
        if ($condition) {
            $this->helperText(fn ($state) => $this->getTaxCalculationPreview($state));
        }

        return $this;
    }
}
