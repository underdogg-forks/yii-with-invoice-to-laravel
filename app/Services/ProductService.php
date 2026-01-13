<?php

namespace App\Services;

use App\DTOs\ProductDTO;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private ProductRepository $repository,
    ) {}

    /**
     * Get product by ID
     */
    public function find(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    /**
     * Get product by SKU
     */
    public function findBySku(string $sku): ?Product
    {
        return $this->repository->findBySku($sku);
    }

    /**
     * Get all active products
     */
    public function getActive(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get products by family
     */
    public function getByFamily(int $familyId): Collection
    {
        return $this->repository->getByFamily($familyId);
    }

    /**
     * Create a new product
     */
    public function create(ProductDTO $dto): Product
    {
        $data = $dto->toArray();
        unset($data['id']);

        // Check if SKU already exists
        if ($this->repository->findBySku($dto->product_sku)) {
            throw new \Exception('Product SKU already exists');
        }

        return $this->repository->create($data);
    }

    /**
     * Update a product
     */
    public function update(int $id, ProductDTO $dto): bool
    {
        $data = $dto->toArray();
        unset($data['id']);

        // Check if SKU already exists (excluding current product)
        $existing = $this->repository->findBySku($dto->product_sku);
        if ($existing && $existing->id !== $id) {
            throw new \Exception('Product SKU already exists');
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete a product
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Search products
     */
    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    /**
     * Activate a product
     */
    public function activate(int $id): bool
    {
        return $this->repository->update($id, ['product_is_active' => true]);
    }

    /**
     * Deactivate a product
     */
    public function deactivate(int $id): bool
    {
        return $this->repository->update($id, ['product_is_active' => false]);
    }
}
