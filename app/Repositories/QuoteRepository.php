<?php

namespace App\Repositories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Collection;

class QuoteRepository
{
    public function findById(int $id): ?Quote
    {
        return Quote::with(['client', 'user', 'status', 'salesOrder'])->find($id);
    }

    public function getAll(): Collection
    {
        return Quote::with(['client', 'user', 'status'])->get();
    }

    public function create(array $data): Quote
    {
        return Quote::create($data);
    }

    public function update(Quote $quote, array $data): Quote
    {
        $quote->update($data);
        return $quote->fresh();
    }

    public function delete(Quote $quote): bool
    {
        return $quote->delete();
    }

    public function getDraft(): Collection
    {
        return Quote::draft()->with(['client', 'user', 'status'])->get();
    }

    public function getApproved(): Collection
    {
        return Quote::approved()->with(['client', 'user', 'status'])->get();
    }

    public function getExpired(): Collection
    {
        return Quote::expired()->with(['client', 'user', 'status'])->get();
    }

    public function getActive(): Collection
    {
        return Quote::active()->with(['client', 'user', 'status'])->get();
    }

    public function search(string $term): Collection
    {
        return Quote::where('quote_number', 'like', "%{$term}%")
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
        return Quote::where('client_id', $clientId)
            ->with(['user', 'status'])
            ->get();
    }

    public function findByUrlKey(string $urlKey): ?Quote
    {
        return Quote::where('url_key', $urlKey)
            ->with(['client', 'user', 'status'])
            ->first();
    }
}
