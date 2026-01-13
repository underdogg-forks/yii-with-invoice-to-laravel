<?php

namespace App\Services;

use App\DTOs\QuoteDTO;
use App\Models\Quote;
use App\Models\QuoteStatus;
use App\Models\SalesOrder;
use App\Repositories\QuoteRepository;
use Illuminate\Support\Str;

class QuoteService
{
    public function __construct(
        private QuoteRepository $repository
    ) {}

    public function create(QuoteDTO $dto): Quote
    {
        $data = $dto->toArray();
        
        // Generate URL key if not provided
        if (empty($data['url_key'])) {
            $data['url_key'] = Str::random(32);
        }
        
        // Set default status to draft
        if (empty($data['status_id'])) {
            $draftStatus = QuoteStatus::where('name', 'draft')->first();
            $data['status_id'] = $draftStatus->id;
        }
        
        // Set quote date if not provided
        if (empty($data['quote_date'])) {
            $data['quote_date'] = now();
        }
        
        return $this->repository->create($data);
    }

    public function update(int $id, QuoteDTO $dto): Quote
    {
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception("Quote not found");
        }
        
        $data = $dto->toArray();
        unset($data['id']);
        
        return $this->repository->update($quote, $data);
    }

    public function delete(int $id): bool
    {
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception("Quote not found");
        }
        
        return $this->repository->delete($quote);
    }

    public function getById(int $id): ?Quote
    {
        return $this->repository->findById($id);
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function search(string $term)
    {
        return $this->repository->search($term);
    }

    public function getDraft()
    {
        return $this->repository->getDraft();
    }

    public function getApproved()
    {
        return $this->repository->getApproved();
    }

    public function getExpired()
    {
        return $this->repository->getExpired();
    }

    public function getActive()
    {
        return $this->repository->getActive();
    }

    public function markAsSent(int $id): Quote
    {
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception("Quote not found");
        }
        
        $sentStatus = QuoteStatus::where('name', 'sent')->first();
        
        return $this->repository->update($quote, [
            'status_id' => $sentStatus->id,
            'sent_at' => now(),
        ]);
    }

    public function markAsViewed(int $id): Quote
    {
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception("Quote not found");
        }
        
        if ($quote->viewed_at) {
            return $quote; // Already viewed
        }
        
        $viewedStatus = QuoteStatus::where('name', 'viewed')->first();
        
        return $this->repository->update($quote, [
            'status_id' => $viewedStatus->id,
            'viewed_at' => now(),
        ]);
    }

    public function approve(int $id, int $userId): Quote
    {
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception("Quote not found");
        }
        
        if (!$quote->canBeApproved()) {
            throw new \Exception("Quote cannot be approved in current status");
        }
        
        $approvedStatus = QuoteStatus::where('name', 'approved')->first();
        
        return $this->repository->update($quote, [
            'status_id' => $approvedStatus->id,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    public function reject(int $id, int $userId, ?string $reason = null): Quote
    {
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception("Quote not found");
        }
        
        $rejectedStatus = QuoteStatus::where('name', 'rejected')->first();
        
        return $this->repository->update($quote, [
            'status_id' => $rejectedStatus->id,
            'rejected_at' => now(),
            'rejected_by' => $userId,
            'rejection_reason' => $reason,
        ]);
    }

    public function convertToSalesOrder(int $id, int $userId): SalesOrder
    {
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception("Quote not found");
        }
        
        if (!$quote->canBeConverted()) {
            throw new \Exception("Quote cannot be converted to Sales Order");
        }
        
        // Create sales order from quote
        $salesOrder = SalesOrder::create([
            'client_id' => $quote->client_id,
            'user_id' => $userId,
            'quote_id' => $quote->id,
            'so_number' => 'SO-' . str_pad(SalesOrder::count() + 1, 4, '0', STR_PAD_LEFT),
            'reference' => $quote->reference,
            'terms' => $quote->terms,
            'footer' => $quote->footer,
            'notes' => $quote->notes,
            'currency_code' => $quote->currency_code,
            'exchange_rate' => $quote->exchange_rate,
            'subtotal' => $quote->subtotal,
            'tax_total' => $quote->tax_total,
            'discount_total' => $quote->discount_total,
            'total' => $quote->total,
            'so_date' => now(),
            'url_key' => Str::random(32),
        ]);
        
        // Update quote with conversion info
        $this->repository->update($quote, [
            'converted_to_so_id' => $salesOrder->id,
        ]);
        
        return $salesOrder;
    }

    public function getByClient(int $clientId)
    {
        return $this->repository->getByClient($clientId);
    }

    public function findByUrlKey(string $urlKey): ?Quote
    {
        return $this->repository->findByUrlKey($urlKey);
    }
}
