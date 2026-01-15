<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatusEnum;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class InvoiceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return Cache::remember('invoice-stats-widget', 300, function () {
            $total = Invoice::count();
            
            if ($total === 0) {
                return $this->getEmptyStats();
            }

            $paid = Invoice::where('status', InvoiceStatusEnum::PAID)->count();
            $pending = Invoice::whereIn('status', [
                InvoiceStatusEnum::SENT,
                InvoiceStatusEnum::VIEWED,
            ])->get();
            
            $overdue = Invoice::where(function ($query) {
                $query->where('date_due', '<', now())
                    ->whereNotIn('status', [InvoiceStatusEnum::PAID, InvoiceStatusEnum::CANCELLED]);
            })->count();

            $pendingAmount = $pending->sum('total_amount');
            $paidPercentage = $total > 0 ? round(($paid / $total) * 100, 1) : 0;

            // Calculate trend (last 30 days vs previous 30 days)
            $currentPeriod = Invoice::where('date_created', '>=', now()->subDays(30))->count();
            $previousPeriod = Invoice::whereBetween('date_created', [
                now()->subDays(60),
                now()->subDays(30)
            ])->count();
            
            $trend = $previousPeriod > 0 
                ? round((($currentPeriod - $previousPeriod) / $previousPeriod) * 100, 1)
                : 0;

            return [
                Stat::make('Total Invoices', number_format($total))
                    ->description($this->getTrendDescription($trend))
                    ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color($trend >= 0 ? 'success' : 'danger')
                    ->chart($this->getLast7DaysChart()),

                Stat::make('Paid Invoices', number_format($paid))
                    ->description("{$paidPercentage}% of total")
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),

                Stat::make('Pending Invoices', $pending->count())
                    ->description('$' . number_format($pendingAmount, 2) . ' total')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('warning'),

                Stat::make('Overdue Invoices', number_format($overdue))
                    ->description($overdue > 0 ? 'Requires attention' : 'All on track')
                    ->descriptionIcon($overdue > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                    ->color($overdue > 0 ? 'danger' : 'success'),
            ];
        });
    }

    protected function getEmptyStats(): array
    {
        return [
            Stat::make('Total Invoices', '0')
                ->description('No invoices yet')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Paid Invoices', '0')
                ->description('0% of total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('gray'),

            Stat::make('Pending Invoices', '0')
                ->description('$0.00 total')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),

            Stat::make('Overdue Invoices', '0')
                ->description('All on track')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('gray'),
        ];
    }

    protected function getTrendDescription(float $trend): string
    {
        if ($trend == 0) {
            return 'No change from last month';
        }

        $direction = $trend > 0 ? 'increase' : 'decrease';
        return abs($trend) . "% {$direction} from last month";
    }

    protected function getLast7DaysChart(): array
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Invoice::whereDate('date_created', $date)->count();
            $data[] = $count;
        }

        return $data;
    }
}
