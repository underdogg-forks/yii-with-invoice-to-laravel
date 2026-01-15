<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomFieldResource\Pages;
use App\Models\CustomField;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomFieldResource extends Resource
{
    protected static ?string $model = CustomField::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('table_name')
                    ->label('Table Name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('The table/entity this custom field applies to (e.g., client, invoice, quote)'),
                
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255)
                    ->helperText('The display label for this custom field'),
                
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        CustomField::TYPE_TEXT => 'Text',
                        CustomField::TYPE_TEXTAREA => 'Textarea',
                        CustomField::TYPE_CHECKBOX => 'Checkbox',
                        CustomField::TYPE_SELECT => 'Select',
                        CustomField::TYPE_DATE => 'Date',
                        CustomField::TYPE_NUMBER => 'Number',
                    ])
                    ->default(CustomField::TYPE_TEXT),
                
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->minValue(0)
                    ->helperText('Display order for this field'),
                
                Forms\Components\Toggle::make('required')
                    ->default(false)
                    ->helperText('Whether this field is required'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('table_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->badge()
                    ->colors([
                        'primary' => CustomField::TYPE_TEXT,
                        'success' => CustomField::TYPE_TEXTAREA,
                        'warning' => CustomField::TYPE_CHECKBOX,
                        'info' => CustomField::TYPE_SELECT,
                        'secondary' => CustomField::TYPE_DATE,
                        'danger' => CustomField::TYPE_NUMBER,
                    ]),
                
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('required')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('table_name')
                    ->options([
                        CustomField::LOCATION_CLIENT => 'Client',
                        CustomField::LOCATION_INVOICE => 'Invoice',
                        CustomField::LOCATION_QUOTE => 'Quote',
                        CustomField::LOCATION_PRODUCT => 'Product',
                    ]),
                
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        CustomField::TYPE_TEXT => 'Text',
                        CustomField::TYPE_TEXTAREA => 'Textarea',
                        CustomField::TYPE_CHECKBOX => 'Checkbox',
                        CustomField::TYPE_SELECT => 'Select',
                        CustomField::TYPE_DATE => 'Date',
                        CustomField::TYPE_NUMBER => 'Number',
                    ]),
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
            ->defaultSort('order', 'asc');
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
            'index' => Pages\ListCustomFields::route('/'),
            'create' => Pages\CreateCustomField::route('/create'),
            'edit' => Pages\EditCustomField::route('/{record}/edit'),
            'view' => Pages\ViewCustomField::route('/{record}'),
        ];
    }
}
