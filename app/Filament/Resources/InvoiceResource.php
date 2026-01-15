<?php

namespace App\Filament\Resources;

use App\DTOs\InvoiceDTO;
use App\Enums\InvoiceStatusEnum;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'client_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('invoice_number')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\DatePicker::make('invoice_date_created')
                            ->required()
                            ->default(now())
                            ->native(false),
                        
                        Forms\Components\DatePicker::make('invoice_date_due')
                            ->required()
                            ->native(false)
                            ->after('invoice_date_created'),
                        
                        Forms\Components\Select::make('invoice_status_id')
                            ->options(InvoiceStatusEnum::class)
                            ->required()
                            ->default(InvoiceStatusEnum::DRAFT->value),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Invoice Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'product_name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->columnSpan(2),
                                
                                Forms\Components\Textarea::make('item_description')
                                    ->maxLength(500)
                                    ->columnSpan(2),
                                
                                Forms\Components\TextInput::make('item_quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->minValue(0.01),
                                
                                Forms\Components\TextInput::make('item_price')
                                    ->numeric()
                                    ->required()
                                    ->live()
                                    ->prefix('$')
                                    ->minValue(0),
                                
                                Forms\Components\Select::make('item_tax_rate_id')
                                    ->relationship('taxRate', 'tax_rate_name')
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                
                                Forms\Components\TextInput::make('item_discount_percent')
                                    ->numeric()
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(0),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->deleteAction(
                                fn (Forms\Components\Actions\Action $action) => $action
                                    ->requiresConfirmation()
                            ),
                    ]),

                Forms\Components\Section::make('Totals')
                    ->schema([
                        Forms\Components\Placeholder::make('subtotal')
                            ->content(function ($get): string {
                                $items = $get('items') ?? [];
                                $subtotal = collect($items)->sum(function ($item) {
                                    $quantity = $item['item_quantity'] ?? 0;
                                    $price = $item['item_price'] ?? 0;
                                    $discount = $item['item_discount_percent'] ?? 0;
                                    $lineTotal = $quantity * $price;
                                    return $lineTotal - ($lineTotal * $discount / 100);
                                });
                                return '$' . number_format($subtotal, 2);
                            }),
                        
                        Forms\Components\Placeholder::make('total')
                            ->content(function ($get): string {
                                // Simplified total calculation
                                $items = $get('items') ?? [];
                                $total = collect($items)->sum(function ($item) {
                                    $quantity = $item['item_quantity'] ?? 0;
                                    $price = $item['item_price'] ?? 0;
                                    $discount = $item['item_discount_percent'] ?? 0;
                                    $lineTotal = $quantity * $price;
                                    return $lineTotal - ($lineTotal * $discount / 100);
                                });
                                return '$' . number_format($total, 2);
                            }),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('invoice_terms')
                            ->maxLength(500)
                            ->columnSpan(2),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client.client_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('invoice_date_created')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('invoice_date_due')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('invoice_status_id')
                    ->badge()
                    ->formatStateUsing(fn ($state) => InvoiceStatusEnum::from($state)->getLabel())
                    ->color(fn ($state) => match($state) {
                        InvoiceStatusEnum::DRAFT->value => 'secondary',
                        InvoiceStatusEnum::SENT->value => 'warning',
                        InvoiceStatusEnum::PAID->value => 'success',
                        InvoiceStatusEnum::OVERDUE->value => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('invoice_total')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('invoice_status_id')
                    ->options(InvoiceStatusEnum::class)
                    ->label('Status'),
                
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->where('invoice_date_due', '<', now()))
                    ->toggle(),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Invoice $record): string => route('invoices.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('send')
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        // Delegate to service
                        app(InvoiceService::class)->sendEmail($record->id);
                    })
                    ->visible(fn (Invoice $record): bool => $record->invoice_status_id === InvoiceStatusEnum::DRAFT->value),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_as_sent')
                        ->icon('heroicon-o-paper-airplane')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['invoice_status_id' => InvoiceStatusEnum::SENT->value]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('invoice_date_created', 'desc');
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('invoice_status_id', InvoiceStatusEnum::DRAFT->value)->count();
        return $count === 0 ? null : (string) $count;
    }
}
