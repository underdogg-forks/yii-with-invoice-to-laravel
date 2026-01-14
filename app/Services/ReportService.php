<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate profit analysis report
     */
    public function generateProfitReport(Carbon $startDate, Carbon $endDate, array $parameters = []): Report
    {
        $data = $this->calculateProfitData($startDate, $endDate, $parameters);
        
        $report = Report::create([
            'type' => 'profit_analysis',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'parameters' => $parameters,
            'data' => $data,
            'generated_by' => Auth::id(),
        ]);
        
        // Generate and save PDF
        $filePath = $this->generateProfitPdf($report, $data);
        $report->update(['file_path' => $filePath]);
        
        return $report->fresh();
    }

    /**
     * Generate sales summary report
     */
    public function generateSalesReport(Carbon $startDate, Carbon $endDate, array $parameters = []): Report
    {
        $data = $this->calculateSalesData($startDate, $endDate, $parameters);
        
        $report = Report::create([
            'type' => 'sales_summary',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'parameters' => $parameters,
            'data' => $data,
            'generated_by' => Auth::id(),
        ]);
        
        // Generate and save PDF
        $filePath = $this->generateSalesPdf($report, $data);
        $report->update(['file_path' => $filePath]);
        
        return $report->fresh();
    }

    /**
     * Generate inventory report
     */
    public function generateInventoryReport(array $parameters = []): Report
    {
        $data = $this->calculateInventoryData($parameters);
        
        $report = Report::create([
            'type' => 'inventory',
            'start_date' => null,
            'end_date' => null,
            'parameters' => $parameters,
            'data' => $data,
            'generated_by' => Auth::id(),
        ]);
        
        // Generate and save PDF
        $filePath = $this->generateInventoryPdf($report, $data);
        $report->update(['file_path' => $filePath]);
        
        return $report->fresh();
    }

    /**
     * Calculate profit data
     */
    private function calculateProfitData(Carbon $startDate, Carbon $endDate, array $parameters): array
    {
        $invoices = Invoice::whereBetween('date_supplied', [$startDate, $endDate])
            ->with('items')
            ->get();
        
        $totalRevenue = $invoices->sum('total_amount');
        $totalCost = 0;
        
        // Calculate costs from invoice items
        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $product = $item->product;
                $cost = $product ? ($product->cost ?? 0) : 0;
                $totalCost += $cost * $item->quantity;
            }
        }
        
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        
        // Group by period
        $groupBy = $parameters['group_by'] ?? 'month';
        $periodData = $this->groupByPeriod($invoices, $groupBy);
        
        return [
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
                'profit_margin' => round($profitMargin, 2),
                'invoice_count' => $invoices->count(),
            ],
            'period_breakdown' => $periodData,
        ];
    }

    /**
     * Calculate sales data
     */
    private function calculateSalesData(Carbon $startDate, Carbon $endDate, array $parameters): array
    {
        $invoices = Invoice::whereBetween('date_supplied', [$startDate, $endDate])
            ->with(['items.product', 'client'])
            ->get();
        
        // Top products
        $productSales = [];
        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $productId = $item->product_id;
                if (!isset($productSales[$productId])) {
                    $productSales[$productId] = [
                        'product_name' => $item->product->name ?? 'Unknown',
                        'quantity' => 0,
                        'revenue' => 0,
                    ];
                }
                $productSales[$productId]['quantity'] += $item->quantity;
                $productSales[$productId]['revenue'] += $item->amount;
            }
        }
        
        // Sort by revenue
        usort($productSales, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        $topProducts = array_slice($productSales, 0, 10);
        
        // Top clients
        $clientSales = [];
        foreach ($invoices as $invoice) {
            $clientId = $invoice->client_id;
            if (!isset($clientSales[$clientId])) {
                $clientSales[$clientId] = [
                    'client_name' => $invoice->client->name ?? 'Unknown',
                    'invoice_count' => 0,
                    'revenue' => 0,
                ];
            }
            $clientSales[$clientId]['invoice_count']++;
            $clientSales[$clientId]['revenue'] += $invoice->total_amount;
        }
        
        // Sort by revenue
        usort($clientSales, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        $topClients = array_slice($clientSales, 0, 10);
        
        return [
            'summary' => [
                'total_revenue' => $invoices->sum('total_amount'),
                'invoice_count' => $invoices->count(),
                'unique_clients' => $invoices->pluck('client_id')->unique()->count(),
            ],
            'top_products' => $topProducts,
            'top_clients' => $topClients,
        ];
    }

    /**
     * Calculate inventory data
     */
    private function calculateInventoryData(array $parameters): array
    {
        $lowStockThreshold = $parameters['low_stock_threshold'] ?? 10;
        
        $products = Product::with('unit')->get();
        
        $totalProducts = $products->count();
        $lowStockProducts = $products->filter(fn($p) => $p->stock_level <= $lowStockThreshold);
        $outOfStockProducts = $products->filter(fn($p) => $p->stock_level == 0);
        
        $productList = $products->map(function ($product) use ($lowStockThreshold) {
            return [
                'name' => $product->name,
                'sku' => $product->sku,
                'stock_level' => $product->stock_level,
                'unit' => $product->unit->name ?? 'N/A',
                'status' => $product->stock_level == 0 ? 'Out of Stock' : 
                           ($product->stock_level <= $lowStockThreshold ? 'Low Stock' : 'In Stock'),
            ];
        })->sortBy('stock_level')->values()->toArray();
        
        return [
            'summary' => [
                'total_products' => $totalProducts,
                'low_stock_count' => $lowStockProducts->count(),
                'out_of_stock_count' => $outOfStockProducts->count(),
            ],
            'products' => $productList,
        ];
    }

    /**
     * Group invoices by period
     */
    private function groupByPeriod($invoices, string $groupBy): array
    {
        $grouped = [];
        
        foreach ($invoices as $invoice) {
            $date = Carbon::parse($invoice->date_supplied);
            
            $key = match($groupBy) {
                'day' => $date->format('Y-m-d'),
                'week' => $date->format('Y-W'),
                'month' => $date->format('Y-m'),
                'quarter' => $date->format('Y-Q'),
                'year' => $date->format('Y'),
                default => $date->format('Y-m'),
            };
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'period' => $key,
                    'revenue' => 0,
                    'invoice_count' => 0,
                ];
            }
            
            $grouped[$key]['revenue'] += $invoice->total_amount;
            $grouped[$key]['invoice_count']++;
        }
        
        return array_values($grouped);
    }

    /**
     * Generate profit PDF (placeholder)
     */
    private function generateProfitPdf(Report $report, array $data): string
    {
        // TODO: Use PdfService to generate actual PDF
        $filename = 'profit_report_' . $report->id . '.pdf';
        $path = 'reports/' . $filename;
        
        // Placeholder: In reality, you'd use the PdfService here
        // Storage::put($path, $pdfContent);
        
        return $path;
    }

    /**
     * Generate sales PDF (placeholder)
     */
    private function generateSalesPdf(Report $report, array $data): string
    {
        $filename = 'sales_report_' . $report->id . '.pdf';
        $path = 'reports/' . $filename;
        
        return $path;
    }

    /**
     * Generate inventory PDF (placeholder)
     */
    private function generateInventoryPdf(Report $report, array $data): string
    {
        $filename = 'inventory_report_' . $report->id . '.pdf';
        $path = 'reports/' . $filename;
        
        return $path;
    }

    /**
     * Get report history
     */
    public function getReportHistory(string $type = null, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        $query = Report::with('generatedBy')->latest();
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->limit($limit)->get();
    }
}
