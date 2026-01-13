<?php

namespace App\Repositories;

use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Collection;

class InvoiceItemRepository
{
    /**
     * Find invoice item by ID
     */
    public function find(int $id): ?InvoiceItem
    {
        return InvoiceItem::with(['product', 'taxRate'])->find($id);
    }

    /**
     * Get all items for an invoice
     */
    public function getByInvoice(int $invoiceId): Collection
    {
        return InvoiceItem::with(['product', 'taxRate'])
            ->where('invoice_id', $invoiceId)
            ->orderBy('item_order')
            ->get();
    }

    /**
     * Create a new invoice item
     */
    public function create(array $data): InvoiceItem
    {
        return InvoiceItem::create($data);
    }

    /**
     * Update an invoice item
     */
    public function update(int $id, array $data): bool
    {
        $item = InvoiceItem::findOrFail($id);
        return $item->update($data);
    }

    /**
     * Delete an invoice item
     */
    public function delete(int $id): bool
    {
        $item = InvoiceItem::findOrFail($id);
        return $item->delete();
    }

    /**
     * Delete all items for an invoice
     */
    public function deleteByInvoice(int $invoiceId): int
    {
        return InvoiceItem::where('invoice_id', $invoiceId)->delete();
    }

    /**
     * Get the next order number for an invoice
     */
    public function getNextOrder(int $invoiceId): int
    {
        $maxOrder = InvoiceItem::where('invoice_id', $invoiceId)->max('item_order');
        return ($maxOrder ?? 0) + 1;
    }
}
