<?php

namespace App\DTOs;

use App\Models\Product;

class ProductDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $family_id = null,
        public ?int $unit_id = null,
        public ?int $tax_rate_id = null,
        public string $product_sku = '',
        public string $product_name = '',
        public string $product_description = '',
        public float $product_price = 0.0,
        public float $purchase_price = 0.0,
        public bool $is_sold_as_service = false,
        public ?string $product_tariff = null,
        public int $sort_order = 0,
    ) {}

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            family_id: $product->family_id,
            unit_id: $product->unit_id,
            tax_rate_id: $product->tax_rate_id,
            product_sku: $product->product_sku ?? '',
            product_name: $product->product_name ?? '',
            product_description: $product->product_description ?? '',
            product_price: $product->product_price ?? 0.0,
            purchase_price: $product->purchase_price ?? 0.0,
            is_sold_as_service: $product->is_sold_as_service ?? false,
            product_tariff: $product->product_tariff,
            sort_order: $product->sort_order ?? 0,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'family_id' => $this->family_id,
            'unit_id' => $this->unit_id,
            'tax_rate_id' => $this->tax_rate_id,
            'product_sku' => $this->product_sku,
            'product_name' => $this->product_name,
            'product_description' => $this->product_description,
            'product_price' => $this->product_price,
            'purchase_price' => $this->purchase_price,
            'is_sold_as_service' => $this->is_sold_as_service,
            'product_tariff' => $this->product_tariff,
            'sort_order' => $this->sort_order,
        ], fn($value) => $value !== null);
    }
}
