<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Enums\QuoteStatusEnum;
use App\Filament\Resources\QuoteResource;
use App\Models\Quote;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('quote_date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state): string => match($state) {
                        QuoteStatusEnum::Draft => 'gray',
                        QuoteStatusEnum::Sent => 'info',
                        QuoteStatusEnum::Viewed => 'warning',
                        QuoteStatusEnum::Approved => 'success',
                        QuoteStatusEnum::Rejected => 'danger',
                        QuoteStatusEnum::Expired => 'danger',
                        default => 'gray',
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
                Tables\Filters\SelectFilter::make('status')
                    ->options(QuoteStatusEnum::class)
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
                    ->visible(fn () => false),
                Tables\Actions\Action::make('convert_to_sales_order')
                    ->icon('heroicon-o-shopping-cart')
                    ->requiresConfirmation()
                    ->action(function (Quote $record) {
                        // TODO: Implement conversion logic
                    })
                    ->visible(fn (Quote $record): bool => $record->status->equals(QuoteStatusEnum::Approved)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('quote_date', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
