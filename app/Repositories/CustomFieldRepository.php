<?php

namespace App\Repositories;

use App\Models\CustomField;
use Illuminate\Database\Eloquent\Collection;

class CustomFieldRepository
{
    public function create(array $data): CustomField
    {
        return CustomField::create($data);
    }

    public function update(CustomField $customField, array $data): CustomField
    {
        $customField->update($data);
        return $customField->fresh();
    }

    public function delete(CustomField $customField): bool
    {
        return $customField->delete();
    }

    public function findById(int $id): ?CustomField
    {
        return CustomField::find($id);
    }

    public function all(): Collection
    {
        return CustomField::orderBy('order')
            ->orderBy('label')
            ->get();
    }

    public function findByType(string $type): Collection
    {
        return CustomField::where('type', $type)
            ->orderBy('order')
            ->orderBy('label')
            ->get();
    }

    public function findByTableName(string $tableName): Collection
    {
        return CustomField::where('table_name', $tableName)
            ->orderBy('order')
            ->orderBy('label')
            ->get();
    }
}
