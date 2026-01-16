<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'product_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\TextInput::make('product_name')
                            ->label('Product Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('product_sku')
                            ->label('SKU')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),
                        
                        Forms\Components\TextInput::make('product_unit')
                            ->label('Unit')
                            ->maxLength(50)
                            ->columnSpan(1),
                        
                        Forms\Components\Textarea::make('product_description')
                            ->label('Description')
                            ->maxLength(500)
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('product_price')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->prefix(config('app.currency_symbol', '$'))
                            ->minValue(0)
                            ->columnSpan(1),
                        
                        Forms\Components\Select::make('tax_rate_id')
                            ->label('Tax Rate')
                            ->relationship('taxRate', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product_sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('product_price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('taxRate.name')
                    ->label('Tax Rate')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('product_name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }
}
