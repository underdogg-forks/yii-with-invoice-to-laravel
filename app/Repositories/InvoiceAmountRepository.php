<?php

namespace App\Repositories;

use App\Models\InvoiceAmount;

class InvoiceAmountRepository
{
    /**
     * Find invoice amount by invoice ID
     */
    public function findByInvoice(int $invoiceId): ?InvoiceAmount
    {
        return InvoiceAmount::where('invoice_id', $invoiceId)->first();
    }

    /**
     * Create a new invoice amount record
     */
    public function create(array $data): InvoiceAmount
    {
        return InvoiceAmount::create($data);
    }

    /**
     * Update an invoice amount record
     */
    public function updateByInvoice(int $invoiceId, array $data): bool
    {
        $amount = $this->findByInvoice($invoiceId);
        
        if ($amount) {
            return $amount->update($data);
        }
        
        // Create if doesn't exist
        $data['invoice_id'] = $invoiceId;
        $this->create($data);
        return true;
    }

    /**
     * Delete an invoice amount record
     */
    public function deleteByInvoice(int $invoiceId): bool
    {
        $amount = $this->findByInvoice($invoiceId);
        return $amount ? $amount->delete() : false;
    }
}
