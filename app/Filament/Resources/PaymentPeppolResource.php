<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentPeppolResource\Pages;
use App\Models\PaymentPeppol;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentPeppolResource extends Resource
{
    protected static ?string $model = PaymentPeppol::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Peppol';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'provider';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('provider')
                    ->label('Provider')
                    ->required()
                    ->maxLength(20),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('updated_at')
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPaymentPeppols::route('/'),
            'create' => Pages\CreatePaymentPeppol::route('/create'),
            'edit' => Pages\EditPaymentPeppol::route('/{record}/edit'),
            'view' => Pages\ViewPaymentPeppol::route('/{record}'),
        ];
    }
}
