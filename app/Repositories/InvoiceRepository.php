<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    /**
     * Find invoice by ID with relationships
     */
    public function find(int $id): ?Invoice
    {
        return Invoice::with([
            'client',
            'numbering',
            'user',
            'status',
            'items.product',
            'items.taxRate',
            'amount',
        ])->find($id);
    }

    /**
     * Find invoice by URL key
     */
    public function findByUrlKey(string $urlKey): ?Invoice
    {
        return Invoice::with([
            'client',
            'items.product',
            'items.taxRate',
            'amount',
        ])->where('url_key', $urlKey)->first();
    }

    /**
     * Get all invoices with pagination
     */
    public function paginate(int $perPage = 15)
    {
        return Invoice::with(['client', 'status', 'amount'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get invoices for a specific client
     */
    public function getByClient(int $clientId): Collection
    {
        return Invoice::with(['status', 'amount'])
            ->where('client_id', $clientId)
            ->orderBy('invoice_date', 'desc')
            ->get();
    }

    /**
     * Get overdue invoices
     */
    public function getOverdue(): Collection
    {
        return Invoice::with(['client', 'amount'])
            ->where('invoice_due_date', '<', now())
            ->where('invoice_status_id', '!=', 'paid')
            ->get();
    }

    /**
     * Create a new invoice
     */
    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    /**
     * Update an invoice
     */
    public function update(int $id, array $data): bool
    {
        $invoice = Invoice::findOrFail($id);
        return $invoice->update($data);
    }

    /**
     * Delete an invoice
     */
    public function delete(int $id): bool
    {
        $invoice = Invoice::findOrFail($id);
        return $invoice->delete();
    }

    /**
     * Get invoices by status
     */
    public function getByStatus(string $statusId): Collection
    {
        return Invoice::with(['client', 'amount'])
            ->where('invoice_status_id', $statusId)
            ->orderBy('invoice_date', 'desc')
            ->get();
    }
}
