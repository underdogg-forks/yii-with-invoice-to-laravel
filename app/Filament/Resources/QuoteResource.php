<?php

namespace App\Filament\Resources;

use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource\Pages;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'quote_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Quote Details')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'client_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->columnSpan(2),
                        
                        Forms\Components\TextInput::make('quote_number')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\DatePicker::make('quote_date')
                            ->required()
                            ->default(now())
                            ->native(false),
                        
                        Forms\Components\DatePicker::make('expiry_date')
                            ->required()
                            ->native(false)
                            ->after('quote_date'),
                        
                        Forms\Components\Select::make('status_id')
                            ->label('Status')
                            ->options(QuoteStatusEnum::forSelect())
                            ->required()
                            ->default(QuoteStatusEnum::DRAFT->value),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Quote Items')
                    ->schema([
                        Forms\Components\Placeholder::make('items_notice')
                            ->content('Quote items will be managed through a separate interface once the quote_items table is created.')
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
                Tables\Columns\TextColumn::make('quote_number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client.client_name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quote_date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status_id')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => QuoteStatusEnum::forSelect()[$state] ?? 'Unknown')
                    ->color(fn ($state): string => match($state) {
                        QuoteStatusEnum::DRAFT->value => 'secondary',
                        QuoteStatusEnum::SENT->value => 'info',
                        QuoteStatusEnum::VIEWED->value => 'warning',
                        QuoteStatusEnum::APPROVED->value => 'success',
                        QuoteStatusEnum::REJECTED->value => 'danger',
                        QuoteStatusEnum::EXPIRED->value => 'danger',
                        default => 'secondary',
                    }),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_id')
                    ->options(QuoteStatusEnum::forSelect())
                    ->label('Status'),
                
                Tables\Filters\Filter::make('quote_date')
                    ->form([
                        Forms\Components\DatePicker::make('quote_from')
                            ->label('Quote Date From'),
                        Forms\Components\DatePicker::make('quote_until')
                            ->label('Quote Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['quote_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('quote_date', '>=', $date),
                            )
                            ->when(
                                $data['quote_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('quote_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Quote $record): string => route('quotes.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn () => false), // Hide until PDF generation is implemented
                Tables\Actions\Action::make('convert_to_sales_order')
                    ->icon('heroicon-o-shopping-cart')
                    ->requiresConfirmation()
                    ->action(function (Quote $record) {
                        // TODO: Implement conversion logic
                    })
                    ->visible(fn (Quote $record): bool => $record->status_id === QuoteStatusEnum::APPROVED->value),
                Tables\Actions\Action::make('send')
                    ->icon('heroicon-o-envelope')
                    ->requiresConfirmation()
                    ->action(function (Quote $record) {
                        // TODO: Implement email sending
                    })
                    ->visible(fn (Quote $record): bool => $record->status_id === QuoteStatusEnum::DRAFT->value),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('quote_date', 'desc');
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
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
            'view' => Pages\ViewQuote::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status_id', QuoteStatusEnum::DRAFT->value)->count();
    }
}
