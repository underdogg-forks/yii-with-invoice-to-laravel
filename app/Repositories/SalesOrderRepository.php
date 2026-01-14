<?php

namespace App\Repositories;

use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Collection;

class SalesOrderRepository
{
    public function findById(int $id): ?SalesOrder
    {
        return SalesOrder::with(['client', 'user', 'status', 'quote', 'invoice'])->find($id);
    }

    public function getAll(): Collection
    {
        return SalesOrder::with(['client', 'user', 'status', 'quote'])->get();
    }

    public function create(array $data): SalesOrder
    {
        return SalesOrder::create($data);
    }

    public function update(SalesOrder $salesOrder, array $data): SalesOrder
    {
        $salesOrder->update($data);
        return $salesOrder->fresh();
    }

    public function delete(SalesOrder $salesOrder): bool
    {
        return $salesOrder->delete();
    }

    public function getPending(): Collection
    {
        return SalesOrder::pending()->with(['client', 'user', 'status'])->get();
    }

    public function getConfirmed(): Collection
    {
        return SalesOrder::confirmed()->with(['client', 'user', 'status'])->get();
    }

    public function getCompleted(): Collection
    {
        return SalesOrder::completed()->with(['client', 'user', 'status'])->get();
    }

    public function getActive(): Collection
    {
        return SalesOrder::active()->with(['client', 'user', 'status'])->get();
    }

    public function search(string $term): Collection
    {
        return SalesOrder::where('so_number', 'like', "%{$term}%")
            ->orWhere('reference', 'like', "%{$term}%")
            ->orWhereHas('client', function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('surname', 'like', "%{$term}%");
            })
            ->with(['client', 'user', 'status'])
            ->get();
    }

    public function getByClient(int $clientId): Collection
    {
        return SalesOrder::where('client_id', $clientId)
            ->with(['user', 'status', 'quote'])
            ->get();
    }

    public function getByQuote(int $quoteId): ?SalesOrder
    {
        return SalesOrder::where('quote_id', $quoteId)
            ->with(['client', 'user', 'status'])
            ->first();
    }

    public function findByUrlKey(string $urlKey): ?SalesOrder
    {
        return SalesOrder::where('url_key', $urlKey)
            ->with(['client', 'user', 'status', 'quote'])
            ->first();
    }
}
