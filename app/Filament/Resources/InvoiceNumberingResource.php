<?php

namespace App\Filament\Resources;

use App\Enums\NumberingEntityTypeEnum;
use App\Filament\Resources\InvoiceNumberingResource\Pages;
use App\Models\InvoiceNumbering;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceNumberingResource extends Resource
{
    protected static ?string $model = InvoiceNumbering::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Numbering Name')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Descriptive name for this numbering scheme')
                    ->columnSpan(2),
                
                Forms\Components\Select::make('entity_type')
                    ->label('Entity Type')
                    ->options(NumberingEntityTypeEnum::options())
                    ->default(NumberingEntityTypeEnum::INVOICE->value)
                    ->required()
                    ->helperText('Apply this numbering scheme to Invoices, Quotes, Clients, Projects, or Tasks')
                    ->columnSpan(2),
                
                Forms\Components\TextInput::make('identifier_format')
                    ->label('Number Format')
                    ->maxLength(100)
                    ->placeholder('INV-{YEAR}-{NUMBER}')
                    ->helperText('Use {NUMBER}, {YEAR}, {MONTH} as placeholders')
                    ->columnSpan(2),
                
                Forms\Components\TextInput::make('next_id')
                    ->label('Next Number')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1)
                    ->helperText('The next invoice number to be generated')
                    ->columnSpan(1),
                
                Forms\Components\TextInput::make('left_pad')
                    ->label('Zero Padding')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(4)
                    ->helperText('Number of digits with zero padding (e.g., 4 = 0001)')
                    ->columnSpan(1),
                
                Forms\Components\Toggle::make('is_default')
                    ->label('Default Numbering')
                    ->helperText('Use this as the default numbering scheme for new invoices')
                    ->default(false)
                    ->columnSpan(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('identifier_format')
                    ->label('Format')
                    ->searchable()
                    ->placeholder('Custom format')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('next_id')
                    ->label('Next Number')
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('invoices_count')
                    ->label('Invoices')
                    ->counts('invoices')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->invoices_count > 0) {
                                    throw new \Exception("Cannot delete numbering scheme '{$record->name}' because it has associated invoices.");
                                }
                            }
                            $records->each->delete();
                        }),
                ]),
            ]);
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
            'index' => Pages\ListInvoiceNumberings::route('/'),
            'create' => Pages\CreateInvoiceNumbering::route('/create'),
            'view' => Pages\ViewInvoiceNumbering::route('/{record}'),
            'edit' => Pages\EditInvoiceNumbering::route('/{record}/edit'),
        ];
    }
}
