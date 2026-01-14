<?php

namespace App\Services;

use App\DTOs\TaxRateDTO;
use App\Models\TaxRate;
use App\Repositories\TaxRateRepository;
use Illuminate\Database\Eloquent\Collection;

class TaxRateService
{
    public function __construct(
        private TaxRateRepository $repository,
    ) {}

    /**
     * Get tax rate by ID
     */
    public function find(int $id): ?TaxRate
    {
        return $this->repository->find($id);
    }

    /**
     * Get all active tax rates
     */
    public function getActive(): Collection
    {
        return $this->repository->getActive();
    }

    /**
     * Get all tax rates
     */
    public function getAll(): Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Create a new tax rate
     */
    public function create(TaxRateDTO $dto): TaxRate
    {
        $data = $dto->toArray();
        unset($data['id']);

        return $this->repository->create($data);
    }

    /**
     * Update a tax rate
     */
    public function update(int $id, TaxRateDTO $dto): bool
    {
        $data = $dto->toArray();
        unset($data['id']);

        return $this->repository->update($id, $data);
    }

    /**
     * Delete a tax rate
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Activate a tax rate
     */
    public function activate(int $id): bool
    {
        return $this->repository->update($id, ['is_active' => true]);
    }

    /**
     * Deactivate a tax rate
     */
    public function deactivate(int $id): bool
    {
        return $this->repository->update($id, ['is_active' => false]);
    }
}
