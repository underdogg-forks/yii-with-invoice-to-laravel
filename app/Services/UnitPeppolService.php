<?php

namespace App\Services;

use App\Repositories\UnitPeppolRepository;
use App\DTOs\UnitPeppolDTO;
use App\Models\UnitPeppol;

class UnitPeppolService
{
    public function __construct(
        private UnitPeppolRepository $repository
    ) {}

    public function getById(int $id): ?UnitPeppol
    {
        return $this->repository->find($id);
    }

    public function getByUnitId(int $unitId): ?UnitPeppol
    {
        return $this->repository->findByUnitId($unitId);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(UnitPeppolDTO $dto): UnitPeppol
    {
        $data = $dto->toArray();
        unset($data['id']);
        
        return $this->repository->create($data);
    }

    public function update(int $id, UnitPeppolDTO $dto): bool
    {
        $unitPeppol = $this->repository->find($id);
        
        if (!$unitPeppol) {
            return false;
        }

        $data = $dto->toArray();
        unset($data['id']);
        
        return $this->repository->update($unitPeppol, $data);
    }

    public function delete(int $id): bool
    {
        $unitPeppol = $this->repository->find($id);
        
        if (!$unitPeppol) {
            return false;
        }

        return $this->repository->delete($unitPeppol);
    }
}
