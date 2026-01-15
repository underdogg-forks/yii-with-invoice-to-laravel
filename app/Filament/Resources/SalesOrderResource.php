<?php

namespace App\Filament\Resources;

use App\Enums\SalesOrderStatusEnum;
use App\Filament\Resources\SalesOrderResource\Pages;
use App\Models\SalesOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'so_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sales Order Details')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'client_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->columnSpan(2),
                        
                        Forms\Components\Select::make('quote_id')
                            ->relationship('quote', 'quote_number')
                            ->searchable()
                            ->preload()
                            ->live(),
                        
                        Forms\Components\TextInput::make('so_number')
                            ->label('SO Number')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\DatePicker::make('order_date')
                            ->required()
                            ->default(now())
                            ->native(false),
                        
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->native(false)
                            ->after('order_date'),
                        
                        Forms\Components\Select::make('status_id')
                            ->label('Status')
                            ->options(SalesOrderStatusEnum::forSelect())
                            ->required()
                            ->default(SalesOrderStatusEnum::PENDING->value),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Sales Order Items')
                    ->schema([
                        Forms\Components\Placeholder::make('items_notice')
                            ->content('Sales order items will be managed through a separate interface once the sales_order_items table is created.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Totals')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('tax_total')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->minValue(0),
                        
                        Forms\Components\TextInput::make('discount_percent')
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100),
                        
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpan(2),
                        
                        Forms\Components\Textarea::make('terms_and_conditions')
                            ->maxLength(65535)
                            ->columnSpan(2),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('so_number')
                    ->label('SO Number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client.client_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status_id')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => SalesOrderStatusEnum::forSelect()[$state] ?? 'Unknown')
                    ->colors([
                        'secondary' => SalesOrderStatusEnum::PENDING->value,
                        'info' => SalesOrderStatusEnum::CONFIRMED->value,
                        'warning' => SalesOrderStatusEnum::PROCESSING->value,
                        'success' => SalesOrderStatusEnum::COMPLETED->value,
                        'danger' => SalesOrderStatusEnum::CANCELLED->value,
                    ]),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->options(SalesOrderStatusEnum::forSelect())
                    ->label('Status'),
                
                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('order_from')
                            ->label('Order Date From'),
                        Forms\Components\DatePicker::make('order_until')
                            ->label('Order Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['order_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date),
                            )
                            ->when(
                                $data['order_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (SalesOrder $record): string => route('sales-orders.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn () => false), // Hide until PDF generation is implemented
                Tables\Actions\Action::make('convert_to_invoice')
                    ->icon('heroicon-o-document-text')
                    ->requiresConfirmation()
                    ->action(function (SalesOrder $record) {
                        // TODO: Implement conversion logic
                    })
                    ->visible(fn (SalesOrder $record): bool => $record->status_id === SalesOrderStatusEnum::COMPLETED->value),
                Tables\Actions\Action::make('confirm_order')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (SalesOrder $record) {
                        $record->update([
                            'status_id' => SalesOrderStatusEnum::CONFIRMED->value,
                            'confirmed_at' => now(),
                            'confirmed_by' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (SalesOrder $record): bool => $record->status_id === SalesOrderStatusEnum::PENDING->value),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_date', 'desc');
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
            'index' => Pages\ListSalesOrders::route('/'),
            'create' => Pages\CreateSalesOrder::route('/create'),
            'edit' => Pages\EditSalesOrder::route('/{record}/edit'),
            'view' => Pages\ViewSalesOrder::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_id', SalesOrderStatusEnum::PENDING->value)->count();
    }
}
