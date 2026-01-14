<?php

namespace App\Services;

use App\Repositories\PaymentPeppolRepository;
use App\DTOs\PaymentPeppolDTO;
use App\Models\PaymentPeppol;

class PaymentPeppolService
{
    public function __construct(
        private PaymentPeppolRepository $repository
    ) {}

    public function getById(int $id): ?PaymentPeppol
    {
        return $this->repository->find($id);
    }

    public function getByInvoiceId(int $invId)
    {
        return $this->repository->findByInvoiceId($invId);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(PaymentPeppolDTO $dto): PaymentPeppol
    {
        $data = $dto->toArray();
        unset($data['id']);
        
        // Set auto_reference to current timestamp if not set
        if (!isset($data['auto_reference']) || $data['auto_reference'] === null) {
            $data['auto_reference'] = time();
        }
        
        return $this->repository->create($data);
    }

    public function update(int $id, PaymentPeppolDTO $dto): bool
    {
        $paymentPeppol = $this->repository->find($id);
        
        if (!$paymentPeppol) {
            return false;
        }

        $data = $dto->toArray();
        unset($data['id']);
        
        return $this->repository->update($paymentPeppol, $data);
    }

    public function delete(int $id): bool
    {
        $paymentPeppol = $this->repository->find($id);
        
        if (!$paymentPeppol) {
            return false;
        }

        return $this->repository->delete($paymentPeppol);
    }
}
