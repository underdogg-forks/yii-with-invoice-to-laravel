<?php

namespace Tests\Fakes;

use App\Models\Quote;
use App\Repositories\QuoteRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Fake QuoteRepository for testing
 * 
 * Provides in-memory storage for quotes without database interaction.
 * Extends the real repository to maintain type compatibility.
 * Preferred over Mockery mocks for better test clarity and maintainability.
 */
class FakeQuoteRepository extends QuoteRepository
{
    private array $quotes = [];
    private int $nextId = 1;

    public function findById(int $id): ?Quote
    {
        return $this->quotes[$id] ?? null;
    }

    public function getAll(): Collection
    {
        return new Collection(array_values($this->quotes));
    }

    public function create(array $data): Quote
    {
        $quote = new Quote($data);
        $quote->id = $this->nextId++;
        $this->quotes[$quote->id] = $quote;
        
        return $quote;
    }

    public function update(Quote $quote, array $data): Quote
    {
        $quote->fill($data);
        $this->quotes[$quote->id] = $quote;
        
        return $quote;
    }

    public function delete(Quote $quote): bool
    {
        if (isset($this->quotes[$quote->id])) {
            unset($this->quotes[$quote->id]);
            return true;
        }
        
        return false;
    }

    public function getDraft(): Collection
    {
        return new Collection(array_filter($this->quotes, function ($quote) {
            return ($quote->status ?? 'draft') === 'draft';
        }));
    }

    public function getApproved(): Collection
    {
        return new Collection(array_filter($this->quotes, function ($quote) {
            return ($quote->status ?? '') === 'approved';
        }));
    }

    public function getExpired(): Collection
    {
        return new Collection(array_filter($this->quotes, function ($quote) {
            return isset($quote->expiry_date) && $quote->expiry_date < now();
        }));
    }

    public function getActive(): Collection
    {
        return new Collection(array_filter($this->quotes, function ($quote) {
            return ($quote->active ?? true);
        }));
    }

    public function search(string $term): Collection
    {
        return new Collection(array_filter($this->quotes, function ($quote) use ($term) {
            return str_contains(strtolower($quote->quote_number ?? ''), strtolower($term))
                || str_contains(strtolower($quote->reference ?? ''), strtolower($term));
        }));
    }

    public function getByClient(int $clientId): Collection
    {
        return new Collection(array_filter($this->quotes, function ($quote) use ($clientId) {
            return ($quote->client_id ?? 0) === $clientId;
        }));
    }

    public function findByUrlKey(string $urlKey): ?Quote
    {
        foreach ($this->quotes as $quote) {
            if (($quote->url_key ?? '') === $urlKey) {
                return $quote;
            }
        }
        
        return null;
    }

    /**
     * Add a pre-existing quote to the repository (for test setup)
     */
    public function add(Quote $quote): void
    {
        if (!$quote->id) {
            $quote->id = $this->nextId++;
        }
        $this->quotes[$quote->id] = $quote;
    }

    /**
     * Reset the repository to empty state
     */
    public function reset(): void
    {
        $this->quotes = [];
        $this->nextId = 1;
    }

    /**
     * Get all quotes (for test assertions)
     */
    public function getAllQuotes(): array
    {
        return $this->quotes;
    }
}
