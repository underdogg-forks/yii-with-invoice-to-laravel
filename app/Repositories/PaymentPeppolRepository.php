<?php

namespace App\Repositories;

use App\Models\PaymentPeppol;
use Illuminate\Database\Eloquent\Collection;

class PaymentPeppolRepository
{
    public function find(int $id): ?PaymentPeppol
    {
        return PaymentPeppol::find($id);
    }

    public function findByInvoiceId(int $invId): Collection
    {
        return PaymentPeppol::where('inv_id', $invId)->get();
    }

    public function all(): Collection
    {
        return PaymentPeppol::with('invoice')->get();
    }

    public function create(array $data): PaymentPeppol
    {
        return PaymentPeppol::create($data);
    }

    public function update(PaymentPeppol $paymentPeppol, array $data): bool
    {
        return $paymentPeppol->update($data);
    }

    public function delete(PaymentPeppol $paymentPeppol): bool
    {
        return $paymentPeppol->delete();
    }
}
