<?php

namespace App\DTOs;

use App\Models\InvoiceNumbering;

class InvoiceNumberingDTO
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $prefix = '',
        public int $next_number = 1,
        public int $padding = 4,
        public bool $is_default = false,
    ) {}

    public static function fromModel(InvoiceNumbering $numbering): self
    {
        return new self(
            id: $numbering->id,
            name: $numbering->name ?? '',
            prefix: $numbering->prefix ?? '',
            next_number: $numbering->next_number ?? 1,
            padding: $numbering->padding ?? 4,
            is_default: $numbering->is_default ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'prefix' => $this->prefix,
            'next_number' => $this->next_number,
            'padding' => $this->padding,
            'is_default' => $this->is_default,
        ];
    }
}
