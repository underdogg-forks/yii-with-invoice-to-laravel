<?php

namespace Tests\Fakes;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Fake UserRepository for testing
 * 
 * Provides in-memory storage for users without database interaction.
 * Extends the real repository to maintain type compatibility.
 * Preferred over Mockery mocks for better test clarity and maintainability.
 */
class FakeUserRepository extends UserRepository
{
    private array $users = [];
    private int $nextId = 1;

    public function find(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if (($user->email ?? '') === $email) {
                return $user;
            }
        }
        
        return null;
    }

    public function findByLogin(string $login): ?User
    {
        foreach ($this->users as $user) {
            if (($user->login ?? '') === $login) {
                return $user;
            }
        }
        
        return null;
    }

    public function all(): Collection
    {
        return new Collection(array_values($this->users));
    }

    public function create(array $data): User
    {
        $user = new User($data);
        $user->id = $this->nextId++;
        $this->users[$user->id] = $user;
        
        return $user;
    }

    public function update(User $user, array $data): bool
    {
        $user->fill($data);
        $this->users[$user->id] = $user;
        
        return true;
    }

    public function delete(User $user): bool
    {
        if (isset($this->users[$user->id])) {
            unset($this->users[$user->id]);
            return true;
        }
        
        return false;
    }

    /**
     * Add a pre-existing user to the repository (for test setup)
     */
    public function add(User $user): void
    {
        if (!$user->id) {
            $user->id = $this->nextId++;
        }
        $this->users[$user->id] = $user;
    }

    /**
     * Reset the repository to empty state
     */
    public function reset(): void
    {
        $this->users = [];
        $this->nextId = 1;
    }

    /**
     * Get all users (for test assertions)
     */
    public function getAll(): array
    {
        return $this->users;
    }
}
