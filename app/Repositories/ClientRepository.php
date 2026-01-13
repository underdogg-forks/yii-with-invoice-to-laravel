<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{
    public function create(array $data): Client
    {
        return Client::create($data);
    }

    public function update(Client $client, array $data): Client
    {
        $client->update($data);
        return $client->fresh();
    }

    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    public function findById(int $id, bool $withTrashed = false): ?Client
    {
        $query = Client::with(['peppol', 'invoices', 'customFields']);
        
        if ($withTrashed) {
            $query->withTrashed();
        }
        
        return $query->find($id);
    }

    public function all(): Collection
    {
        return Client::with(['peppol', 'customFields'])
            ->orderBy('client_name')
            ->get();
    }

    public function findActive(): Collection
    {
        return Client::active()
            ->with(['peppol', 'customFields'])
            ->orderBy('client_name')
            ->get();
    }

    public function findInactive(): Collection
    {
        return Client::inactive()
            ->with(['peppol', 'customFields'])
            ->orderBy('client_name')
            ->get();
    }

    public function findByGroup(string $group): Collection
    {
        return Client::byGroup($group)
            ->with(['peppol', 'customFields'])
            ->orderBy('client_name')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Client::search($query)
            ->with(['peppol', 'customFields'])
            ->orderBy('client_name')
            ->get();
    }

    public function restore(int $id): Client
    {
        $client = Client::withTrashed()->findOrFail($id);
        $client->restore();
        return $client->fresh();
    }

    public function forceDelete(Client $client): bool
    {
        return $client->forceDelete();
    }
}
