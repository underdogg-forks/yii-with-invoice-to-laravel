<?php

namespace App\Repositories;

use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Collection;

class TaxRateRepository
{
    /**
     * Find tax rate by ID
     */
    public function find(int $id): ?TaxRate
    {
        return TaxRate::find($id);
    }

    /**
     * Get all active tax rates
     */
    public function getActive(): Collection
    {
        return TaxRate::where('is_active', true)
            ->orderBy('tax_rate_name')
            ->get();
    }

    /**
     * Get all tax rates
     */
    public function getAll(): Collection
    {
        return TaxRate::orderBy('tax_rate_name')->get();
    }

    /**
     * Create a new tax rate
     */
    public function create(array $data): TaxRate
    {
        return TaxRate::create($data);
    }

    /**
     * Update a tax rate
     */
    public function update(int $id, array $data): bool
    {
        $taxRate = TaxRate::findOrFail($id);
        return $taxRate->update($data);
    }

    /**
     * Delete a tax rate
     */
    public function delete(int $id): bool
    {
        $taxRate = TaxRate::findOrFail($id);
        return $taxRate->delete();
    }
}
