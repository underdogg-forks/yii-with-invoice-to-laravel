<?php

namespace Tests\Fakes;

use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Fake ClientRepository for testing
 * 
 * Provides in-memory storage for clients without database interaction.
 * Extends the real repository to maintain type compatibility.
 * Preferred over Mockery mocks for better test clarity and maintainability.
 */
class FakeClientRepository extends ClientRepository
{
    private array $clients = [];
    private int $nextId = 1;

    public function create(array $data): Client
    {
        $client = new Client($data);
        $client->client_id = $this->nextId++;
        $this->clients[$client->client_id] = $client;
        
        return $client;
    }

    public function update(Client $client, array $data): Client
    {
        $client->fill($data);
        $this->clients[$client->client_id] = $client;
        
        return $client;
    }

    public function delete(Client $client): bool
    {
        if (isset($this->clients[$client->client_id])) {
            unset($this->clients[$client->client_id]);
            return true;
        }
        
        return false;
    }

    public function findById(int $id, bool $withTrashed = false): ?Client
    {
        return $this->clients[$id] ?? null;
    }

    public function all(): Collection
    {
        return new Collection(array_values($this->clients));
    }

    public function findActive(): Collection
    {
        return new Collection(array_filter($this->clients, function ($client) {
            return $client->client_active ?? true;
        }));
    }

    public function findInactive(): Collection
    {
        return new Collection(array_filter($this->clients, function ($client) {
            return !($client->client_active ?? true);
        }));
    }

    public function findByGroup(string $group): Collection
    {
        return new Collection(array_filter($this->clients, function ($client) use ($group) {
            return ($client->client_group ?? '') === $group;
        }));
    }

    public function search(string $query): Collection
    {
        return new Collection(array_filter($this->clients, function ($client) use ($query) {
            return str_contains(strtolower($client->client_name ?? ''), strtolower($query))
                || str_contains(strtolower($client->client_email ?? ''), strtolower($query));
        }));
    }

    public function restore(int $id): Client
    {
        // For fake implementation, just return the client if it exists
        return $this->clients[$id] ?? new Client(['client_id' => $id]);
    }

    public function forceDelete(Client $client): bool
    {
        return $this->delete($client);
    }

    /**
     * Add a pre-existing client to the repository (for test setup)
     */
    public function add(Client $client): void
    {
        if (!$client->client_id) {
            $client->client_id = $this->nextId++;
        }
        $this->clients[$client->client_id] = $client;
    }

    /**
     * Reset the repository to empty state
     */
    public function reset(): void
    {
        $this->clients = [];
        $this->nextId = 1;
    }

    /**
     * Get all clients (for test assertions)
     */
    public function getAll(): array
    {
        return $this->clients;
    }
}
