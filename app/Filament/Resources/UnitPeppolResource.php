<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitPeppolResource\Pages;
use App\Models\UnitPeppol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UnitPeppolResource extends Resource
{
    protected static ?string $model = UnitPeppol::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'Peppol';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(3)
                    ->uppercase()
                    ->columnSpan(1),
                
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(120)
                    ->columnSpan(1),
                
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpan(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                
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
            ->defaultSort('code', 'asc');
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
            'index' => Pages\ListUnitPeppols::route('/'),
            'create' => Pages\CreateUnitPeppol::route('/create'),
            'edit' => Pages\EditUnitPeppol::route('/{record}/edit'),
            'view' => Pages\ViewUnitPeppol::route('/{record}'),
        ];
    }
}
