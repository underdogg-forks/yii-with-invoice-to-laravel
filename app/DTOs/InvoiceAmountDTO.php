<?php

namespace App\DTOs;

use App\Models\InvoiceAmount;

class InvoiceAmountDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $invoice_id = null,
        public float $item_subtotal = 0.0,
        public float $item_tax_total = 0.0,
        public float $invoice_discount_total = 0.0,
        public float $invoice_total = 0.0,
        public float $invoice_paid = 0.0,
        public float $invoice_balance = 0.0,
    ) {}

    public static function fromModel(InvoiceAmount $amount): self
    {
        return new self(
            id: $amount->id,
            invoice_id: $amount->invoice_id,
            item_subtotal: $amount->item_subtotal ?? 0.0,
            item_tax_total: $amount->item_tax_total ?? 0.0,
            invoice_discount_total: $amount->discount ?? 0.0,
            invoice_total: $amount->total ?? 0.0,
            invoice_paid: $amount->paid ?? 0.0,
            invoice_balance: $amount->balance ?? 0.0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'item_subtotal' => $this->item_subtotal,
            'item_tax_total' => $this->item_tax_total,
            'discount' => $this->invoice_discount_total,
            'total' => $this->invoice_total,
            'paid' => $this->invoice_paid,
            'balance' => $this->invoice_balance,
        ];
    }

    /**
     * Calculate invoice balance
     */
    public function calculateBalance(): void
    {
        $this->invoice_balance = $this->invoice_total - $this->invoice_paid;
    }
}
