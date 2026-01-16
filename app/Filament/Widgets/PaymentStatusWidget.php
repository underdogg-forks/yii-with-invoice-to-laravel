<?php

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatusEnum;
use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class PaymentStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Invoice Status Distribution';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return Cache::remember('payment-status-chart', 300, function () {
            $statusCounts = $this->getStatusCounts();

            if (array_sum($statusCounts) === 0) {
                return $this->getEmptyData();
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Invoices by Status',
                        'data' => array_values($statusCounts),
                        'backgroundColor' => [
                            'rgb(156, 163, 175)', // gray - Draft
                            'rgb(251, 191, 36)',  // warning/yellow - Sent
                            'rgb(34, 197, 94)',   // success/green - Paid
                            'rgb(239, 68, 68)',   // danger/red - Overdue
                        ],
                        'borderColor' => 'rgb(255, 255, 255)',
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => array_keys($statusCounts),
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }

    protected function getStatusCounts(): array
    {
        $draft = Invoice::where('status', InvoiceStatusEnum::DRAFT)->count();
        $sent = Invoice::whereIn('status', [
            InvoiceStatusEnum::SENT,
            InvoiceStatusEnum::VIEWED,
        ])->count();
        $paid = Invoice::where('status', InvoiceStatusEnum::PAID)->count();
        
        $overdue = Invoice::where(function ($query) {
            $query->where('date_due', '<', now())
                ->whereNotIn('status', [InvoiceStatusEnum::PAID, InvoiceStatusEnum::CANCELLED]);
        })->count();

        return [
            'Draft' => $draft,
            'Sent' => $sent,
            'Paid' => $paid,
            'Overdue' => $overdue,
        ];
    }

    protected function getEmptyData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'No Data',
                    'data' => [1],
                    'backgroundColor' => ['rgb(229, 231, 235)'],
                ],
            ],
            'labels' => ['No Invoices'],
        ];
    }
}
