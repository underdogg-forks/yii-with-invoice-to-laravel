<?php

namespace App\Repositories;

use App\Models\ClientPeppol;
use Illuminate\Database\Eloquent\Collection;

class ClientPeppolRepository
{
    public function find(int $id): ?ClientPeppol
    {
        return ClientPeppol::find($id);
    }

    public function findByClientId(int $clientId): ?ClientPeppol
    {
        return ClientPeppol::where('client_id', $clientId)->first();
    }

    public function all(): Collection
    {
        return ClientPeppol::with('client')->get();
    }

    public function create(array $data): ClientPeppol
    {
        return ClientPeppol::create($data);
    }

    public function update(ClientPeppol $clientPeppol, array $data): bool
    {
        return $clientPeppol->update($data);
    }

    public function delete(ClientPeppol $clientPeppol): bool
    {
        return $clientPeppol->delete();
    }
}
