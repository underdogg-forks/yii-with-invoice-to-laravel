<?php

namespace App\Services;

use App\DTOs\InvoiceDTO;
use App\Models\Invoice;
use App\Models\InvoiceNumbering;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(
        private InvoiceRepository $repository,
        private InvoiceAmountService $amountService,
    ) {}

    /**
     * Get invoice by ID
     */
    public function find(int $id): ?Invoice
    {
        return $this->repository->find($id);
    }

    /**
     * Get invoice by URL key (for guest access)
     */
    public function findByUrlKey(string $urlKey): ?Invoice
    {
        return $this->repository->findByUrlKey($urlKey);
    }

    /**
     * Create a new invoice
     */
    public function create(InvoiceDTO $dto): Invoice
    {
        $data = $dto->toArray();
        unset($data['id']);

        // Generate invoice number if not provided
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = $this->generateInvoiceNumber($dto->numbering_id);
        }

        // Generate URL key for guest access
        if (empty($data['url_key'])) {
            $data['url_key'] = Str::random(32);
        }

        // Set default status
        if (empty($data['invoice_status_id'])) {
            $data['invoice_status_id'] = 'draft';
        }

        $invoice = $this->repository->create($data);

        // Initialize invoice amount
        $this->amountService->initializeForInvoice($invoice->id);

        return $invoice;
    }

    /**
     * Update an invoice
     */
    public function update(int $id, InvoiceDTO $dto): bool
    {
        $data = $dto->toArray();
        unset($data['id']);

        $result = $this->repository->update($id, $data);

        // Recalculate amounts if needed
        $this->amountService->recalculate($id);

        return $result;
    }

    /**
     * Delete an invoice
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Change invoice status
     */
    public function changeStatus(int $id, string $statusId): bool
    {
        return $this->repository->update($id, ['invoice_status_id' => $statusId]);
    }

    /**
     * Generate invoice number based on numbering scheme
     */
    private function generateInvoiceNumber(?int $numberingId): string
    {
        $numbering = InvoiceNumbering::findOrFail($numberingId);
        
        $number = str_pad($numbering->next_number, $numbering->padding, '0', STR_PAD_LEFT);
        $invoiceNumber = $numbering->prefix . $number;

        // Increment next number
        $numbering->increment('next_number');

        return $invoiceNumber;
    }

    /**
     * Create a credit invoice from an existing invoice
     */
    public function createCreditInvoice(int $parentId): Invoice
    {
        $parentInvoice = $this->repository->find($parentId);
        
        if (!$parentInvoice) {
            throw new \Exception('Parent invoice not found');
        }

        $dto = InvoiceDTO::fromModel($parentInvoice);
        $dto->id = null;
        $dto->parent_id = $parentId;
        $dto->invoice_number = ''; // Will be generated
        $dto->invoice_date = now()->format('Y-m-d');
        $dto->invoice_status_id = 'draft';

        return $this->create($dto);
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(int $id): bool
    {
        $invoice = $this->repository->find($id);
        
        if (!$invoice || !$invoice->invoice_due_date) {
            return false;
        }

        return $invoice->invoice_due_date->isPast() 
            && $invoice->invoice_status_id !== 'paid';
    }

    /**
     * Get guest URL for invoice
     */
    public function getGuestUrl(int $id): ?string
    {
        $invoice = $this->repository->find($id);
        
        if (!$invoice || !$invoice->url_key) {
            return null;
        }

        return route('invoice.guest', ['key' => $invoice->url_key]);
    }
}
