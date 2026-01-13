<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * Find product by ID
     */
    public function find(int $id): ?Product
    {
        return Product::with(['family', 'taxRate'])->find($id);
    }

    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?Product
    {
        return Product::where('product_sku', $sku)->first();
    }

    /**
     * Get all active products
     */
    public function getActive(): Collection
    {
        return Product::with(['family', 'taxRate'])
            ->where('product_is_active', true)
            ->orderBy('product_name')
            ->get();
    }

    /**
     * Get all products with pagination
     */
    public function paginate(int $perPage = 15)
    {
        return Product::with(['family', 'taxRate'])
            ->orderBy('product_name')
            ->paginate($perPage);
    }

    /**
     * Get products by family
     */
    public function getByFamily(int $familyId): Collection
    {
        return Product::where('family_id', $familyId)
            ->where('product_is_active', true)
            ->orderBy('product_name')
            ->get();
    }

    /**
     * Create a new product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update a product
     */
    public function update(int $id, array $data): bool
    {
        $product = Product::findOrFail($id);
        return $product->update($data);
    }

    /**
     * Delete a product
     */
    public function delete(int $id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    /**
     * Search products by name or SKU
     */
    public function search(string $query): Collection
    {
        return Product::where('product_name', 'like', "%{$query}%")
            ->orWhere('product_sku', 'like', "%{$query}%")
            ->where('product_is_active', true)
            ->limit(20)
            ->get();
    }
}
