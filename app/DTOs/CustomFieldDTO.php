<?php

namespace App\DTOs;

class CustomFieldDTO
{
    public function __construct(
        public ?int $id = null,
        public string $label = '',
        public string $type = 'text', // text, textarea, checkbox, select, date, number
        public ?string $table_name = null,
        public ?string $options = null, // JSON string for select options
        public int $order = 0,
        public bool $is_required = false,
    ) {}

    public static function fromModel($customField): self
    {
        return new self(
            id: $customField->id,
            label: $customField->label,
            type: $customField->type,
            table_name: $customField->table_name,
            options: $customField->options,
            order: $customField->order,
            is_required: (bool) $customField->is_required,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type,
            'table_name' => $this->table_name,
            'options' => $this->options,
            'order' => $this->order,
            'is_required' => $this->is_required,
        ], fn($value) => $value !== null);
    }
}
