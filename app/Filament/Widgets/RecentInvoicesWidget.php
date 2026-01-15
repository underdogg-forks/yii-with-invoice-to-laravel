<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentInvoicesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->with(['client'])
                    ->latest('date_created')
                    ->limit(5)
            )
            ->heading('Recent Invoices')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('client.computed_full_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('date_created')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => match ($state->value) {
                        'draft' => 'gray',
                        'sent' => 'info',
                        'viewed' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable()
                    ->weight('semibold'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Invoice $record): string => route('filament.admin.resources.invoices.view', ['record' => $record->id]))
                    ->openUrlInNewTab(false),
            ])
            ->headerActions([
                Tables\Actions\Action::make('view_all')
                    ->label('View All Invoices')
                    ->icon('heroicon-m-arrow-right')
                    ->url(route('filament.admin.resources.invoices.index'))
                    ->button(),
            ])
            ->defaultSort('date_created', 'desc')
            ->emptyStateHeading('No invoices yet')
            ->emptyStateDescription('Create your first invoice to get started.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
