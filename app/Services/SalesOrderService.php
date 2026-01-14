<?php

namespace App\Services;

use App\DTOs\SalesOrderDTO;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\SalesOrderStatus;
use App\Repositories\SalesOrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesOrderService
{
    public function __construct(
        private SalesOrderRepository $repository
    ) {}

    public function create(SalesOrderDTO $dto): SalesOrder
    {
        $data = $dto->toArray();
        
        // Generate URL key if not provided
        if (empty($data['url_key'])) {
            $data['url_key'] = Str::random(32);
        }
        
        // Set default status to pending
        if (empty($data['status_id'])) {
            $pendingStatus = SalesOrderStatus::where('name', 'pending')->first();
            $data['status_id'] = $pendingStatus->id;
        }
        
        // Set SO date if not provided
        if (empty($data['so_date'])) {
            $data['so_date'] = now();
        }
        
        return $this->repository->create($data);
    }

    public function update(int $id, SalesOrderDTO $dto): SalesOrder
    {
        $salesOrder = $this->repository->findById($id);
        
        if (!$salesOrder) {
            throw new \Exception("Sales Order not found");
        }
        
        $data = $dto->toArray();
        unset($data['id']);
        
        return $this->repository->update($salesOrder, $data);
    }

    public function delete(int $id): bool
    {
        $salesOrder = $this->repository->findById($id);
        
        if (!$salesOrder) {
            throw new \Exception("Sales Order not found");
        }
        
        return $this->repository->delete($salesOrder);
    }

    public function getById(int $id): ?SalesOrder
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

    public function getPending()
    {
        return $this->repository->getPending();
    }

    public function getConfirmed()
    {
        return $this->repository->getConfirmed();
    }

    public function getCompleted()
    {
        return $this->repository->getCompleted();
    }

    public function getActive()
    {
        return $this->repository->getActive();
    }

    public function confirm(int $id, int $userId): SalesOrder
    {
        $salesOrder = $this->repository->findById($id);
        
        if (!$salesOrder) {
            throw new \Exception("Sales Order not found");
        }
        
        if (!$salesOrder->canBeConfirmed()) {
            throw new \Exception("Sales Order cannot be confirmed in current status");
        }
        
        $confirmedStatus = SalesOrderStatus::where('name', 'confirmed')->first();
        
        return $this->repository->update($salesOrder, [
            'status_id' => $confirmedStatus->id,
            'confirmed_at' => now(),
            'confirmed_by' => $userId,
        ]);
    }

    public function markAsProcessing(int $id): SalesOrder
    {
        $salesOrder = $this->repository->findById($id);
        
        if (!$salesOrder) {
            throw new \Exception("Sales Order not found");
        }
        
        $processingStatus = SalesOrderStatus::where('name', 'processing')->first();
        
        return $this->repository->update($salesOrder, [
            'status_id' => $processingStatus->id,
            'processing_at' => now(),
        ]);
    }

    public function complete(int $id, int $userId): SalesOrder
    {
        $salesOrder = $this->repository->findById($id);
        
        if (!$salesOrder) {
            throw new \Exception("Sales Order not found");
        }
        
        if (!$salesOrder->canBeCompleted()) {
            throw new \Exception("Sales Order cannot be completed in current status");
        }
        
        $completedStatus = SalesOrderStatus::where('name', 'completed')->first();
        
        return $this->repository->update($salesOrder, [
            'status_id' => $completedStatus->id,
            'completed_at' => now(),
            'completed_by' => $userId,
        ]);
    }

    public function cancel(int $id, int $userId, ?string $reason = null): SalesOrder
    {
        $salesOrder = $this->repository->findById($id);
        
        if (!$salesOrder) {
            throw new \Exception("Sales Order not found");
        }
        
        $cancelledStatus = SalesOrderStatus::where('name', 'cancelled')->first();
        
        return $this->repository->update($salesOrder, [
            'status_id' => $cancelledStatus->id,
            'cancelled_at' => now(),
            'cancelled_by' => $userId,
            'cancellation_reason' => $reason,
        ]);
    }

    public function convertToInvoice(int $id, int $userId): Invoice
    {
        $salesOrder = $this->repository->findById($id);
        
        if (!$salesOrder) {
            throw new \Exception("Sales Order not found");
        }
        
        if (!$salesOrder->canBeConverted()) {
            throw new \Exception("Sales Order cannot be converted to Invoice");
        }
        
        // Create invoice from sales order with atomic number generation
        $invoiceNumber = DB::transaction(function () {
            $nextNumber = Invoice::lockForUpdate()->max('id') + 1;
            return 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
        
        $invoice = Invoice::create([
            'client_id' => $salesOrder->client_id,
            'user_id' => $userId,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'terms' => $salesOrder->terms,
            'footer' => $salesOrder->footer,
            'notes' => $salesOrder->notes,
            'currency_code' => $salesOrder->currency_code,
            'exchange_rate' => $salesOrder->exchange_rate,
            'url_key' => Str::random(32),
        ]);
        
        // Update sales order with conversion info
        $this->repository->update($salesOrder, [
            'converted_to_invoice_id' => $invoice->id,
        ]);
        
        return $invoice;
    }

    public function getByClient(int $clientId)
    {
        return $this->repository->getByClient($clientId);
    }

    public function getByQuote(int $quoteId): ?SalesOrder
    {
        return $this->repository->getByQuote($quoteId);
    }

    public function findByUrlKey(string $urlKey): ?SalesOrder
    {
        return $this->repository->findByUrlKey($urlKey);
    }
}
