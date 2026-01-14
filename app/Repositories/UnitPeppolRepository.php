<?php

namespace App\Repositories;

use App\Models\UnitPeppol;
use Illuminate\Database\Eloquent\Collection;

class UnitPeppolRepository
{
    public function find(int $id): ?UnitPeppol
    {
        return UnitPeppol::find($id);
    }

    public function findByUnitId(int $unitId): ?UnitPeppol
    {
        return UnitPeppol::where('unit_id', $unitId)->first();
    }

    public function all(): Collection
    {
        return UnitPeppol::with('unit')->get();
    }

    public function create(array $data): UnitPeppol
    {
        return UnitPeppol::create($data);
    }

    public function update(UnitPeppol $unitPeppol, array $data): bool
    {
        return $unitPeppol->update($data);
    }

    public function delete(UnitPeppol $unitPeppol): bool
    {
        return $unitPeppol->delete();
    }
}
