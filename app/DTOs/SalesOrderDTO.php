<?php

namespace App\DTOs;

use App\Models\SalesOrder;

class SalesOrderDTO
{
    public function __construct(
        public ?int $id = null,
        public int $client_id,
        public int $user_id,
        public ?int $quote_id = null,
        public ?int $status_id = null,
        public string $so_number,
        public ?string $reference = null,
        public ?string $terms = null,
        public ?string $footer = null,
        public ?string $notes = null,
        public ?string $currency_code = 'USD',
        public ?float $exchange_rate = 1.0,
        public ?float $subtotal = 0.0,
        public ?float $tax_total = 0.0,
        public ?float $discount_total = 0.0,
        public ?float $total = 0.0,
        public ?\DateTime $so_date = null,
        public ?\DateTime $confirmed_at = null,
        public ?\DateTime $processing_at = null,
        public ?\DateTime $completed_at = null,
        public ?\DateTime $cancelled_at = null,
        public ?int $confirmed_by = null,
        public ?int $completed_by = null,
        public ?int $cancelled_by = null,
        public ?string $cancellation_reason = null,
        public ?string $url_key = null,
        public ?int $converted_to_invoice_id = null,
    ) {}

    public static function fromModel(SalesOrder $salesOrder): self
    {
        return new self(
            id: $salesOrder->id,
            client_id: $salesOrder->client_id,
            user_id: $salesOrder->user_id,
            quote_id: $salesOrder->quote_id,
            status_id: $salesOrder->status_id,
            so_number: $salesOrder->so_number,
            reference: $salesOrder->reference,
            terms: $salesOrder->terms,
            footer: $salesOrder->footer,
            notes: $salesOrder->notes,
            currency_code: $salesOrder->currency_code,
            exchange_rate: $salesOrder->exchange_rate,
            subtotal: $salesOrder->subtotal,
            tax_total: $salesOrder->tax_total,
            discount_total: $salesOrder->discount_total,
            total: $salesOrder->total,
            so_date: $salesOrder->so_date,
            confirmed_at: $salesOrder->confirmed_at,
            processing_at: $salesOrder->processing_at,
            completed_at: $salesOrder->completed_at,
            cancelled_at: $salesOrder->cancelled_at,
            confirmed_by: $salesOrder->confirmed_by,
            completed_by: $salesOrder->completed_by,
            cancelled_by: $salesOrder->cancelled_by,
            cancellation_reason: $salesOrder->cancellation_reason,
            url_key: $salesOrder->url_key,
            converted_to_invoice_id: $salesOrder->converted_to_invoice_id,
        );
    }

    public function toArray(): array
    {
        $data = [
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'quote_id' => $this->quote_id,
            'so_number' => $this->so_number,
            'reference' => $this->reference,
            'terms' => $this->terms,
            'footer' => $this->footer,
            'notes' => $this->notes,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'subtotal' => $this->subtotal,
            'tax_total' => $this->tax_total,
            'discount_total' => $this->discount_total,
            'total' => $this->total,
            'so_date' => $this->so_date,
            'confirmed_at' => $this->confirmed_at,
            'processing_at' => $this->processing_at,
            'completed_at' => $this->completed_at,
            'cancelled_at' => $this->cancelled_at,
            'confirmed_by' => $this->confirmed_by,
            'completed_by' => $this->completed_by,
            'cancelled_by' => $this->cancelled_by,
            'cancellation_reason' => $this->cancellation_reason,
            'url_key' => $this->url_key,
            'converted_to_invoice_id' => $this->converted_to_invoice_id,
        ];

        if ($this->status_id !== null) {
            $data['status_id'] = $this->status_id;
        }

        return $data;
    }
}
