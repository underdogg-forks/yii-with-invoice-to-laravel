<?php

namespace App\DTOs;

use App\Models\Product;

class ProductDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $family_id = null,
        public ?int $tax_rate_id = null,
        public string $product_sku = '',
        public string $product_name = '',
        public string $product_description = '',
        public float $product_price = 0.0,
        public ?string $product_unit = null,
        public bool $product_is_active = true,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            family_id: $product->family_id,
            tax_rate_id: $product->tax_rate_id,
            product_sku: $product->product_sku ?? '',
            product_name: $product->product_name ?? '',
            product_description: $product->product_description ?? '',
            product_price: $product->product_price ?? 0.0,
            product_unit: $product->product_unit,
            product_is_active: $product->product_is_active ?? true,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'family_id' => $this->family_id,
            'tax_rate_id' => $this->tax_rate_id,
            'product_sku' => $this->product_sku,
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'product_price' => $this->product_price,
            'product_unit' => $this->product_unit,
            'product_is_active' => $this->product_is_active,
        ], fn($value) => $value !== null);
    }
}
