<?php

namespace App\Services;

use App\DTOs\InvoiceAmountDTO;
use App\Models\InvoiceAmount;
use App\Repositories\InvoiceAmountRepository;
use App\Repositories\InvoiceItemRepository;
use App\Repositories\InvoiceRepository;

class InvoiceAmountService
{
    public function __construct(
        private InvoiceAmountRepository $repository,
        private InvoiceItemRepository $itemRepository,
        private InvoiceRepository $invoiceRepository,
    ) {}

    /**
     * Get invoice amount by invoice ID
     */
    public function findByInvoice(int $invoiceId): ?InvoiceAmount
    {
        return $this->repository->findByInvoice($invoiceId);
    }

    /**
     * Initialize invoice amount for a new invoice
     */
    public function initializeForInvoice(int $invoiceId): InvoiceAmount
    {
        $dto = new InvoiceAmountDTO(invoice_id: $invoiceId);
        return $this->repository->create($dto->toArray());
    }

    /**
     * Recalculate invoice amounts based on items and invoice settings
     */
    public function recalculate(int $invoiceId): bool
    {
        $invoice = $this->invoiceRepository->find($invoiceId);
        
        if (!$invoice) {
            return false;
        }

        $items = $this->itemRepository->getByInvoice($invoiceId);

        // Calculate item subtotal and tax total
        $itemSubtotal = $items->sum('item_subtotal');
        $itemTaxTotal = $items->sum('item_tax_amount');

        // Calculate invoice-level discount
        $invoiceDiscountTotal = 0.0;
        if ($invoice->invoice_discount_percent > 0) {
            $invoiceDiscountTotal = $itemSubtotal * ($invoice->invoice_discount_percent / 100);
        } elseif ($invoice->invoice_discount_amount > 0) {
            $invoiceDiscountTotal = $invoice->invoice_discount_amount;
        }

        // Calculate invoice total
        $invoiceTotal = $itemSubtotal + $itemTaxTotal - $invoiceDiscountTotal;

        // Get current paid amount
        $currentAmount = $this->repository->findByInvoice($invoiceId);
        $invoicePaid = $currentAmount->invoice_paid ?? 0.0;

        // Calculate balance
        $invoiceBalance = $invoiceTotal - $invoicePaid;

        $dto = new InvoiceAmountDTO(
            invoice_id: $invoiceId,
            item_subtotal: $itemSubtotal,
            item_tax_total: $itemTaxTotal,
            invoice_discount_total: $invoiceDiscountTotal,
            invoice_total: $invoiceTotal,
            invoice_paid: $invoicePaid,
            invoice_balance: $invoiceBalance,
        );

        return $this->repository->updateByInvoice($invoiceId, $dto->toArray());
    }

    /**
     * Record a payment
     */
    public function recordPayment(int $invoiceId, float $amount): bool
    {
        $currentAmount = $this->repository->findByInvoice($invoiceId);
        
        if (!$currentAmount) {
            return false;
        }

        $newPaid = $currentAmount->invoice_paid + $amount;
        $newBalance = $currentAmount->invoice_total - $newPaid;

        return $this->repository->updateByInvoice($invoiceId, [
            'invoice_paid' => $newPaid,
            'invoice_balance' => $newBalance,
        ]);
    }

    /**
     * Check if invoice is fully paid
     */
    public function isFullyPaid(int $invoiceId): bool
    {
        $amount = $this->repository->findByInvoice($invoiceId);
        return $amount && $amount->invoice_balance <= 0.01; // Account for floating point precision
    }
}
