<?php

namespace App\DTOs;

use App\Models\TaxRate;

class TaxRateDTO
{
    public function __construct(
        public ?int $id = null,
        public string $tax_rate_name = '',
        public float $tax_rate_percent = 0.0,
        public bool $is_active = true,
    ) {}

    public static function fromModel(TaxRate $taxRate): self
    {
        return new self(
            id: $taxRate->id,
            tax_rate_name: $taxRate->name ?? '',
            tax_rate_percent: (float) ($taxRate->rate ?? 0.0),
            is_active: $taxRate->is_default ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->tax_rate_name,
            'rate' => $this->tax_rate_percent,
            'is_default' => $this->is_active,
        ];
    }
}
