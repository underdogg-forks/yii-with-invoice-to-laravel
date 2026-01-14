<?php

namespace App\DTOs;

class UnitPeppolDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $unit_id = null,
        public string $code = '',
        public string $name = '',
        public string $description = '',
    ) {}

    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            unit_id: $model->unit_id,
            code: $model->code ?? '',
            name: $model->name ?? '',
            description: $model->description ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'unit_id' => $this->unit_id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
