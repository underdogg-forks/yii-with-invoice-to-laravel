<?php

namespace App\DTOs;

class ClientCustomDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $client_id = null,
        public ?int $custom_field_id = null,
        public ?string $value = null,
    ) {}

    public static function fromModel($clientCustom): self
    {
        return new self(
            id: $clientCustom->id,
            client_id: $clientCustom->client_id,
            custom_field_id: $clientCustom->custom_field_id,
            value: $clientCustom->value,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'custom_field_id' => $this->custom_field_id,
            'value' => $this->value,
        ], fn($value) => $value !== null);
    }
}
