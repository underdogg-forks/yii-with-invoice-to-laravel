<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatusEnum;
use App\Models\Client;
use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopClientsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTopClientsQuery())
            ->heading('Top Clients by Revenue')
            ->columns([
                TextColumn::make('client_name')
                    ->label('Client')
                    ->searchable()
                    ->weight('bold')
                    ->limit(40)
                    ->getStateUsing(fn (Client $record) => $record->computed_full_name),

                TextColumn::make('invoices_count')
                    ->label('Invoices')
                    ->alignCenter()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => number_format($state)),

                TextColumn::make('total_revenue')
                    ->label('Total Revenue')
                    ->money('USD')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View Client')
                    ->icon('heroicon-m-user')
                    ->url(fn (Client $record): string => route('filament.admin.resources.clients.view', ['record' => $record->id]))
                    ->openUrlInNewTab(false),
            ])
            ->defaultSort('total_revenue', 'desc')
            ->emptyStateHeading('No client data available')
            ->emptyStateDescription('Paid invoices will appear here once created.')
            ->emptyStateIcon('heroicon-o-users');
    }

    protected function getTopClientsQuery(): Builder
    {
        return Client::query()
            ->select([
                'clients.id',
                'clients.name',
                'clients.surname',
                'clients.full_name',
                DB::raw('COUNT(invoices.id) as invoices_count'),
                DB::raw('SUM(invoices.total_amount) as total_revenue'),
            ])
            ->join('invoices', 'clients.id', '=', 'invoices.client_id')
            ->where('invoices.status', InvoiceStatusEnum::PAID)
            ->groupBy('clients.id', 'clients.name', 'clients.surname', 'clients.full_name')
            ->orderByDesc('total_revenue')
            ->limit(5);
    }
}
