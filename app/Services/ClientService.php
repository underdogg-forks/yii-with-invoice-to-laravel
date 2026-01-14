<?php

namespace App\Services;

use App\DTOs\ClientDTO;
use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;

class ClientService
{
    public function __construct(
        private ClientRepository $repository
    ) {}

    public function create(ClientDTO $dto): Client
    {
        $data = $dto->toArray();
        unset($data['id']);
        
        return $this->repository->create($data);
    }

    public function update(int $id, ClientDTO $dto): Client
    {
        $data = $dto->toArray();
        unset($data['id']);
        
        $client = $this->repository->findById($id);
        return $this->repository->update($client, $data);
    }

    public function delete(int $id): bool
    {
        $client = $this->repository->findById($id);
        return $this->repository->delete($client);
    }

    public function findById(int $id): ?Client
    {
        return $this->repository->findById($id);
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getActive(): Collection
    {
        return $this->repository->findActive();
    }

    public function getInactive(): Collection
    {
        return $this->repository->findInactive();
    }

    public function getByGroup(string $group): Collection
    {
        return $this->repository->findByGroup($group);
    }

    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }

    public function activate(int $id): Client
    {
        $client = $this->repository->findById($id);
        return $this->repository->update($client, ['client_active' => true]);
    }

    public function deactivate(int $id): Client
    {
        $client = $this->repository->findById($id);
        return $this->repository->update($client, ['client_active' => false]);
    }

    public function restore(int $id): Client
    {
        return $this->repository->restore($id);
    }

    public function forceDelete(int $id): bool
    {
        $client = $this->repository->findById($id, true);
        return $this->repository->forceDelete($client);
    }
}
