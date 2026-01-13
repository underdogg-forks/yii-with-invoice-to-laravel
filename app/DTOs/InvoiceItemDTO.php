<?php

namespace App\DTOs;

use App\Models\InvoiceItem;

class InvoiceItemDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $invoice_id = null,
        public ?int $product_id = null,
        public ?int $tax_rate_id = null,
        public string $item_name = '',
        public string $item_description = '',
        public float $item_quantity = 1.0,
        public float $item_price = 0.0,
        public float $item_discount_percent = 0.0,
        public float $item_discount_amount = 0.0,
        public float $item_tax_percent = 0.0,
        public float $item_tax_amount = 0.0,
        public float $item_subtotal = 0.0,
        public float $item_total = 0.0,
        public int $item_order = 0,
        public ?string $item_sku = null,
    ) {}

    public static function fromModel(InvoiceItem $item): self
    {
        return new self(
            id: $item->id,
            invoice_id: $item->invoice_id,
            product_id: $item->product_id,
            tax_rate_id: $item->tax_rate_id,
            item_name: $item->item_name ?? '',
            item_description: $item->item_description ?? '',
            item_quantity: $item->item_quantity ?? 1.0,
            item_price: $item->item_price ?? 0.0,
            item_discount_percent: $item->item_discount_percent ?? 0.0,
            item_discount_amount: $item->item_discount_amount ?? 0.0,
            item_tax_percent: $item->item_tax_percent ?? 0.0,
            item_tax_amount: $item->item_tax_amount ?? 0.0,
            item_subtotal: $item->item_subtotal ?? 0.0,
            item_total: $item->item_total ?? 0.0,
            item_order: $item->item_order ?? 0,
            item_sku: $item->item_sku,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'product_id' => $this->product_id,
            'tax_rate_id' => $this->tax_rate_id,
            'item_name' => $this->item_name,
            'item_description' => $this->item_description,
            'item_quantity' => $this->item_quantity,
            'item_price' => $this->item_price,
            'item_discount_percent' => $this->item_discount_percent,
            'item_discount_amount' => $this->item_discount_amount,
            'item_tax_percent' => $this->item_tax_percent,
            'item_tax_amount' => $this->item_tax_amount,
            'item_subtotal' => $this->item_subtotal,
            'item_total' => $this->item_total,
            'item_order' => $this->item_order,
            'item_sku' => $this->item_sku,
        ], fn($value) => $value !== null);
    }

    /**
     * Calculate item amounts based on quantity, price, discount, and tax
     */
    public function calculateAmounts(): void
    {
        // Calculate subtotal (quantity * price)
        $this->item_subtotal = $this->item_quantity * $this->item_price;

        // Apply discount
        if ($this->item_discount_percent > 0) {
            $this->item_discount_amount = $this->item_subtotal * ($this->item_discount_percent / 100);
        }

        $subtotalAfterDiscount = $this->item_subtotal - $this->item_discount_amount;

        // Calculate tax
        if ($this->item_tax_percent > 0) {
            $this->item_tax_amount = $subtotalAfterDiscount * ($this->item_tax_percent / 100);
        }

        // Calculate total
        $this->item_total = $subtotalAfterDiscount + $this->item_tax_amount;
    }
}
