<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxRateResource\Pages;
use App\Models\TaxRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaxRateResource extends Resource
{
    protected static ?string $model = TaxRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tax Rate Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tax Rate Name')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('rate')
                            ->label('Tax Rate Percent')
                            ->numeric()
                            ->required()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->columnSpan(1),
                        
                        Forms\Components\Toggle::make('is_default')
                            ->label('Is Default')
                            ->default(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tax Rate Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('rate')
                    ->label('Tax Rate Percent')
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->suffix('%')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('is_default')
                    ->label('Is Default')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Default' : 'Not Default')
                    ->color(fn ($state) => $state ? 'success' : 'secondary'),
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
            ->defaultSort('name', 'asc');
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
            'index' => Pages\ListTaxRates::route('/'),
            'create' => Pages\CreateTaxRate::route('/create'),
            'edit' => Pages\EditTaxRate::route('/{record}/edit'),
            'view' => Pages\ViewTaxRate::route('/{record}'),
        ];
    }
}
