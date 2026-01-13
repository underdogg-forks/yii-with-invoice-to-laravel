<?php

namespace App\Services;

use App\Repositories\ClientPeppolRepository;
use App\DTOs\ClientPeppolDTO;
use App\Models\ClientPeppol;

class ClientPeppolService
{
    public function __construct(
        private ClientPeppolRepository $repository
    ) {}

    public function getById(int $id): ?ClientPeppol
    {
        return $this->repository->find($id);
    }

    public function getByClientId(int $clientId): ?ClientPeppol
    {
        return $this->repository->findByClientId($clientId);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(ClientPeppolDTO $dto): ClientPeppol
    {
        $data = $dto->toArray();
        unset($data['id']); // Remove ID for creation
        
        return $this->repository->create($data);
    }

    public function update(int $id, ClientPeppolDTO $dto): bool
    {
        $clientPeppol = $this->repository->find($id);
        
        if (!$clientPeppol) {
            return false;
        }

        $data = $dto->toArray();
        unset($data['id']); // Don't update ID
        
        return $this->repository->update($clientPeppol, $data);
    }

    public function delete(int $id): bool
    {
        $clientPeppol = $this->repository->find($id);
        
        if (!$clientPeppol) {
            return false;
        }

        return $this->repository->delete($clientPeppol);
    }
}
