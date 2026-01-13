<?php

namespace App\DTOs;

use App\Models\Invoice;

class InvoiceDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $client_id = null,
        public ?int $numbering_id = null,
        public ?int $user_id = null,
        public ?int $parent_id = null, // For credit invoices
        public ?int $quote_id = null,
        public ?int $sales_order_id = null,
        public string $invoice_number = '',
        public ?string $invoice_date = null,
        public ?string $invoice_due_date = null,
        public ?string $invoice_status_id = null,
        public string $invoice_terms = '',
        public string $invoice_footer = '',
        public string $currency_code = 'USD',
        public float $exchange_rate = 1.0,
        public float $invoice_discount_percent = 0.0,
        public float $invoice_discount_amount = 0.0,
        public ?string $notes = null,
        public ?string $terms_and_conditions = null,
        public ?string $url_key = null,
        public ?string $guest_url = null,
        public bool $is_read_only = false,
        public ?string $payment_method = null,
        public ?string $po_number = null,
        public ?string $buyer_reference = null,
        public array $custom_fields = [],
    ) {}

    public static function fromModel(Invoice $invoice): self
    {
        return new self(
            id: $invoice->id,
            client_id: $invoice->client_id,
            numbering_id: $invoice->numbering_id,
            user_id: $invoice->user_id,
            parent_id: $invoice->parent_id,
            quote_id: $invoice->quote_id,
            sales_order_id: $invoice->sales_order_id,
            invoice_number: $invoice->invoice_number ?? '',
            invoice_date: $invoice->invoice_date?->format('Y-m-d'),
            invoice_due_date: $invoice->invoice_due_date?->format('Y-m-d'),
            invoice_status_id: $invoice->invoice_status_id,
            invoice_terms: $invoice->invoice_terms ?? '',
            invoice_footer: $invoice->invoice_footer ?? '',
            currency_code: $invoice->currency_code ?? 'USD',
            exchange_rate: $invoice->exchange_rate ?? 1.0,
            invoice_discount_percent: $invoice->invoice_discount_percent ?? 0.0,
            invoice_discount_amount: $invoice->invoice_discount_amount ?? 0.0,
            notes: $invoice->notes,
            terms_and_conditions: $invoice->terms_and_conditions,
            url_key: $invoice->url_key,
            guest_url: $invoice->guest_url,
            is_read_only: $invoice->is_read_only ?? false,
            payment_method: $invoice->payment_method,
            po_number: $invoice->po_number,
            buyer_reference: $invoice->buyer_reference,
            custom_fields: $invoice->custom_fields ?? [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'numbering_id' => $this->numbering_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'quote_id' => $this->quote_id,
            'sales_order_id' => $this->sales_order_id,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date,
            'invoice_due_date' => $this->invoice_due_date,
            'invoice_status_id' => $this->invoice_status_id,
            'invoice_terms' => $this->invoice_terms,
            'invoice_footer' => $this->invoice_footer,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'invoice_discount_percent' => $this->invoice_discount_percent,
            'invoice_discount_amount' => $this->invoice_discount_amount,
            'notes' => $this->notes,
            'terms_and_conditions' => $this->terms_and_conditions,
            'url_key' => $this->url_key,
            'guest_url' => $this->guest_url,
            'is_read_only' => $this->is_read_only,
            'payment_method' => $this->payment_method,
            'po_number' => $this->po_number,
            'buyer_reference' => $this->buyer_reference,
            'custom_fields' => $this->custom_fields,
        ], fn($value) => $value !== null);
    }
}
