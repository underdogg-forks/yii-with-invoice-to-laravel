<?php

namespace Tests\Fakes;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

/**
 * Fake InvoiceRepository for testing
 * 
 * Provides in-memory storage for invoices without database interaction.
 * Preferred over Mockery mocks for better test clarity and maintainability.
 */
class FakeInvoiceRepository
{
    private array $invoices = [];
    private int $nextId = 1;

    public function find(int $id): ?Invoice
    {
        return $this->invoices[$id] ?? null;
    }

    public function findByUrlKey(string $urlKey): ?Invoice
    {
        foreach ($this->invoices as $invoice) {
            if (($invoice->url_key ?? '') === $urlKey) {
                return $invoice;
            }
        }
        
        return null;
    }

    public function paginate(int $perPage = 15)
    {
        // For testing, just return a collection
        return new Collection(array_values($this->invoices));
    }

    public function getByClient(int $clientId): Collection
    {
        return new Collection(array_filter($this->invoices, function ($invoice) use ($clientId) {
            return ($invoice->client_id ?? 0) === $clientId;
        }));
    }

    public function getOverdue(): Collection
    {
        return new Collection(array_filter($this->invoices, function ($invoice) {
            // Simple check - in real test you'd use Carbon
            return isset($invoice->invoice_due_date) && 
                   $invoice->invoice_due_date < now() &&
                   ($invoice->invoice_status_id ?? '') !== 'paid';
        }));
    }

    public function create(array $data): Invoice
    {
        $invoice = new Invoice($data);
        $invoice->id = $this->nextId++;
        $this->invoices[$invoice->id] = $invoice;
        
        return $invoice;
    }

    public function update(int $id, array $data): bool
    {
        if (isset($this->invoices[$id])) {
            $this->invoices[$id]->fill($data);
            return true;
        }
        
        return false;
    }

    public function delete(int $id): bool
    {
        if (isset($this->invoices[$id])) {
            unset($this->invoices[$id]);
            return true;
        }
        
        return false;
    }

    public function getByStatus(string $statusId): Collection
    {
        return new Collection(array_filter($this->invoices, function ($invoice) use ($statusId) {
            return ($invoice->invoice_status_id ?? '') === $statusId;
        }));
    }

    /**
     * Add a pre-existing invoice to the repository (for test setup)
     */
    public function add(Invoice $invoice): void
    {
        if (!$invoice->id) {
            $invoice->id = $this->nextId++;
        }
        $this->invoices[$invoice->id] = $invoice;
    }

    /**
     * Reset the repository to empty state
     */
    public function reset(): void
    {
        $this->invoices = [];
        $this->nextId = 1;
    }

    /**
     * Get all invoices (for test assertions)
     */
    public function getAll(): array
    {
        return $this->invoices;
    }

    /**
     * Alias for backwards compatibility with tests that use findById
     */
    public function findById(int $id): ?Invoice
    {
        return $this->find($id);
    }

    /**
     * Alias for backwards compatibility with tests that use getAll
     */
    public function getAllInvoices(): Collection
    {
        return new Collection(array_values($this->invoices));
    }
}
