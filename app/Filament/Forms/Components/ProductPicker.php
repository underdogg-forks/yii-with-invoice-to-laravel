<?php

namespace App\Filament\Forms\Components;

use App\Models\Product;
use Filament\Forms\Components\Select;

class ProductPicker extends Select
{
    protected string $view = 'filament.forms.components.product-picker';

    protected function setUp(): void
    {
        parent::setUp();

        $this->relationship('product', 'product_name')
            ->searchable(['product_name', 'product_sku', 'product_description'])
            ->preload()
            ->live()
            ->getSearchResultsUsing(function (string $search) {
                return Product::query()
                    ->where(function ($query) use ($search) {
                        $query->where('product_name', 'like', "%{$search}%")
                            ->orWhere('product_sku', 'like', "%{$search}%")
                            ->orWhere('product_description', 'like', "%{$search}%");
                    })
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn (Product $product) => [
                        $product->id => $this->formatProductLabel($product)
                    ]);
            })
            ->getOptionLabelFromRecordUsing(fn (Product $record) => $this->formatProductLabel($record))
            ->afterStateUpdated(function ($state, callable $set) {
                if (!$state) {
                    return;
                }

                $product = Product::find($state);
                if (!$product) {
                    return;
                }

                // Auto-fill related fields
                $set('item_description', $product->product_description);
                $set('item_price', $product->product_price);
                $set('item_tax_rate_id', $product->tax_rate_id);
                $set('item_quantity', 1);
            })
            ->createOptionForm([
                \Filament\Forms\Components\Section::make('Product Information')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('product_name')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('product_sku')
                            ->label('SKU')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        \Filament\Forms\Components\Textarea::make('product_description')
                            ->rows(3)
                            ->maxLength(500),
                        \Filament\Forms\Components\TextInput::make('product_price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        \Filament\Forms\Components\TextInput::make('product_unit')
                            ->maxLength(20),
                        \Filament\Forms\Components\Select::make('tax_rate_id')
                            ->relationship('taxRate', 'tax_rate_name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
            ])
            ->helperText('Search by name, SKU, or description');
    }

    protected function formatProductLabel(Product $product): string
    {
        $parts = array_filter([
            $product->product_sku ? "[{$product->product_sku}]" : null,
            $product->product_name,
            $product->product_price ? '$' . number_format($product->product_price, 2) : null,
        ]);

        return implode(' • ', $parts);
    }

    public function withAutoFill(bool $condition = true): static
    {
        if (!$condition) {
            $this->afterStateUpdated(null);
        }

        return $this;
    }
}
