<?php

namespace App\DTOs;

use App\Models\Quote;

class QuoteDTO
{
    public function __construct(
        public ?int $id = null,
        public int $client_id,
        public int $user_id,
        public ?int $status_id = null,
        public string $quote_number,
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
        public ?\DateTime $quote_date = null,
        public ?\DateTime $expires_at = null,
        public ?\DateTime $sent_at = null,
        public ?\DateTime $viewed_at = null,
        public ?\DateTime $approved_at = null,
        public ?\DateTime $rejected_at = null,
        public ?int $approved_by = null,
        public ?int $rejected_by = null,
        public ?string $rejection_reason = null,
        public ?string $url_key = null,
        public ?int $converted_to_so_id = null,
    ) {}

    public static function fromModel(Quote $quote): self
    {
        return new self(
            id: $quote->id,
            client_id: $quote->client_id,
            user_id: $quote->user_id,
            status_id: $quote->status_id,
            quote_number: $quote->quote_number,
            reference: $quote->reference,
            terms: $quote->terms,
            footer: $quote->footer,
            notes: $quote->notes,
            currency_code: $quote->currency_code,
            exchange_rate: $quote->exchange_rate,
            subtotal: $quote->subtotal,
            tax_total: $quote->tax_total,
            discount_total: $quote->discount_total,
            total: $quote->total,
            quote_date: $quote->quote_date,
            expires_at: $quote->expires_at,
            sent_at: $quote->sent_at,
            viewed_at: $quote->viewed_at,
            approved_at: $quote->approved_at,
            rejected_at: $quote->rejected_at,
            approved_by: $quote->approved_by,
            rejected_by: $quote->rejected_by,
            rejection_reason: $quote->rejection_reason,
            url_key: $quote->url_key,
            converted_to_so_id: $quote->converted_to_so_id,
        );
    }

    public function toArray(): array
    {
        $data = [
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'quote_number' => $this->quote_number,
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
            'quote_date' => $this->quote_date,
            'expires_at' => $this->expires_at,
            'sent_at' => $this->sent_at,
            'viewed_at' => $this->viewed_at,
            'approved_at' => $this->approved_at,
            'rejected_at' => $this->rejected_at,
            'approved_by' => $this->approved_by,
            'rejected_by' => $this->rejected_by,
            'rejection_reason' => $this->rejection_reason,
            'url_key' => $this->url_key,
            'converted_to_so_id' => $this->converted_to_so_id,
        ];

        if ($this->status_id !== null) {
            $data['status_id'] = $this->status_id;
        }

        return $data;
    }
}
