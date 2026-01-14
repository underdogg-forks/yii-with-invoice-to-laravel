<?php

namespace App\Services;

use App\DTOs\CustomFieldDTO;
use App\DTOs\ClientCustomDTO;
use App\Models\CustomField;
use App\Models\ClientCustom;
use App\Repositories\CustomFieldRepository;
use Illuminate\Database\Eloquent\Collection;

class CustomFieldService
{
    public function __construct(
        private CustomFieldRepository $repository
    ) {}

    public function create(CustomFieldDTO $dto): CustomField
    {
        $data = $dto->toArray();
        unset($data['id']);
        
        // Validate field type
        $this->validateFieldType($data['type']);
        
        return $this->repository->create($data);
    }

    public function update(int $id, CustomFieldDTO $dto): CustomField
    {
        $data = $dto->toArray();
        unset($data['id']);
        
        // Validate field type if provided
        if (isset($data['type'])) {
            $this->validateFieldType($data['type']);
        }
        
        $customField = $this->repository->findById($id);
        return $this->repository->update($customField, $data);
    }

    public function delete(int $id): bool
    {
        $customField = $this->repository->findById($id);
        return $this->repository->delete($customField);
    }

    public function findById(int $id): ?CustomField
    {
        return $this->repository->findById($id);
    }

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getByType(string $type): Collection
    {
        return $this->repository->findByType($type);
    }

    public function getByTableName(string $tableName): Collection
    {
        return $this->repository->findByTableName($tableName);
    }

    public function setClientCustomValue(ClientCustomDTO $dto): ClientCustom
    {
        // Check if value already exists
        $existing = ClientCustom::where('client_id', $dto->client_id)
            ->where('custom_field_id', $dto->custom_field_id)
            ->first();

        if ($existing) {
            $existing->update(['value' => $dto->value]);
            return $existing;
        }

        return ClientCustom::create($dto->toArray());
    }

    public function getClientCustomValues(int $clientId): Collection
    {
        return ClientCustom::where('client_id', $clientId)
            ->with('customField')
            ->get();
    }

    private function validateFieldType(string $type): void
    {
        $validTypes = ['text', 'textarea', 'checkbox', 'select', 'date', 'number'];
        
        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException(
                "Invalid field type: {$type}. Valid types are: " . implode(', ', $validTypes)
            );
        }
    }
}
