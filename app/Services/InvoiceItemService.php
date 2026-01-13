<?php

namespace App\Services;

use App\DTOs\InvoiceItemDTO;
use App\Models\InvoiceItem;
use App\Repositories\InvoiceItemRepository;
use Illuminate\Database\Eloquent\Collection;

class InvoiceItemService
{
    public function __construct(
        private InvoiceItemRepository $repository,
        private InvoiceAmountService $amountService,
    ) {}

    /**
     * Get invoice item by ID
     */
    public function find(int $id): ?InvoiceItem
    {
        return $this->repository->find($id);
    }

    /**
     * Get all items for an invoice
     */
    public function getByInvoice(int $invoiceId): Collection
    {
        return $this->repository->getByInvoice($invoiceId);
    }

    /**
     * Create a new invoice item
     */
    public function create(InvoiceItemDTO $dto): InvoiceItem
    {
        $data = $dto->toArray();
        unset($data['id']);

        // Calculate amounts
        $dto->calculateAmounts();
        $data = $dto->toArray();
        unset($data['id']);

        // Set order if not provided
        if (empty($data['item_order'])) {
            $data['item_order'] = $this->repository->getNextOrder($dto->invoice_id);
        }

        $item = $this->repository->create($data);

        // Recalculate invoice amounts
        $this->amountService->recalculate($dto->invoice_id);

        return $item;
    }

    /**
     * Update an invoice item
     */
    public function update(int $id, InvoiceItemDTO $dto): bool
    {
        $item = $this->repository->find($id);
        
        if (!$item) {
            return false;
        }

        // Calculate amounts
        $dto->calculateAmounts();
        
        $data = $dto->toArray();
        unset($data['id']);

        $result = $this->repository->update($id, $data);

        // Recalculate invoice amounts
        $this->amountService->recalculate($item->invoice_id);

        return $result;
    }

    /**
     * Delete an invoice item
     */
    public function delete(int $id): bool
    {
        $item = $this->repository->find($id);
        
        if (!$item) {
            return false;
        }

        $invoiceId = $item->invoice_id;
        $result = $this->repository->delete($id);

        // Recalculate invoice amounts
        $this->amountService->recalculate($invoiceId);

        return $result;
    }

    /**
     * Create invoice item from product
     */
    public function createFromProduct(int $invoiceId, int $productId, float $quantity = 1.0): InvoiceItem
    {
        $product = \App\Models\Product::findOrFail($productId);
        
        $dto = new InvoiceItemDTO(
            invoice_id: $invoiceId,
            product_id: $productId,
            tax_rate_id: $product->tax_rate_id,
            item_name: $product->product_name,
            item_description: $product->product_description,
            item_quantity: $quantity,
            item_price: $product->product_price,
            item_tax_percent: $product->taxRate?->tax_rate_percent ?? 0.0,
            item_sku: $product->product_sku,
        );

        return $this->create($dto);
    }

    /**
     * Recalculate item amounts
     */
    public function recalculateItem(int $id): bool
    {
        $item = $this->repository->find($id);
        
        if (!$item) {
            return false;
        }

        $dto = InvoiceItemDTO::fromModel($item);
        $dto->calculateAmounts();

        return $this->repository->update($id, $dto->toArray());
    }
}
