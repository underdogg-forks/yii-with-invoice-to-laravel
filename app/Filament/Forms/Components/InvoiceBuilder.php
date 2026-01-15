<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;

class InvoiceBuilder extends Repeater
{
    protected string $view = 'filament.forms.components.invoice-builder';

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema([
            ProductPicker::make('product_id')
                ->label('Product')
                ->columnSpan(2)
                ->withAutoFill(),

            Textarea::make('item_description')
                ->label('Description')
                ->rows(2)
                ->maxLength(500)
                ->columnSpan(2),

            TextInput::make('item_quantity')
                ->label('Qty')
                ->numeric()
                ->default(1)
                ->required()
                ->live(onBlur: true)
                ->minValue(0.01)
                ->afterStateUpdated(fn ($state, callable $set, Get $get) => 
                    $this->calculateLineTotal($set, $get)
                ),

            CurrencyInput::make('item_price')
                ->label('Price')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set, Get $get) => 
                    $this->calculateLineTotal($set, $get)
                ),

            TaxRateSelector::make('item_tax_rate_id')
                ->label('Tax Rate')
                ->live()
                ->afterStateUpdated(fn ($state, callable $set, Get $get) => 
                    $this->calculateLineTotal($set, $get)
                ),

            TextInput::make('item_discount_percent')
                ->label('Discount')
                ->numeric()
                ->suffix('%')
                ->minValue(0)
                ->maxValue(100)
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set, Get $get) => 
                    $this->calculateLineTotal($set, $get)
                ),

            TextInput::make('item_total')
                ->label('Total')
                ->disabled()
                ->dehydrated()
                ->numeric()
                ->prefix('$')
                ->columnSpan(1),
        ])
            ->columns(4)
            ->defaultItems(1)
            ->reorderable()
            ->collapsible()
            ->cloneable()
            ->itemLabel(fn (array $state): ?string => 
                $state['item_description'] ?? 'New Item'
            )
            ->addActionLabel('Add Line Item')
            ->deleteAction(
                fn ($action) => $action
                    ->requiresConfirmation()
                    ->modalHeading('Delete line item?')
                    ->modalDescription('Are you sure you want to delete this line item?')
            )
            ->live();
    }

    protected function calculateLineTotal(callable $set, Get $get): void
    {
        $quantity = (float) ($get('item_quantity') ?? 0);
        $price = (float) ($get('item_price') ?? 0);
        $discountPercent = (float) ($get('item_discount_percent') ?? 0);

        $subtotal = $quantity * $price;
        $discountAmount = $subtotal * ($discountPercent / 100);
        $afterDiscount = $subtotal - $discountAmount;

        // Tax calculation
        $taxRateId = $get('item_tax_rate_id');
        if ($taxRateId) {
            $taxRate = \App\Models\TaxRate::find($taxRateId);
            if ($taxRate) {
                $taxAmount = $afterDiscount * ($taxRate->tax_rate_percent / 100);
                $total = $afterDiscount + $taxAmount;
            } else {
                $total = $afterDiscount;
            }
        } else {
            $total = $afterDiscount;
        }

        $set('item_total', number_format($total, 2, '.', ''));
    }

    public function withTotalsCalculation(bool $condition = true): static
    {
        // This method can be used to enable/disable totals calculation
        return $this;
    }
}
