<?php

namespace App\DTOs;

class PaymentPeppolDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $inv_id = null,
        public ?int $auto_reference = null,
        public string $provider = '',
    ) {}

    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            inv_id: $model->inv_id,
            auto_reference: $model->auto_reference,
            provider: $model->provider ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'inv_id' => $this->inv_id,
            'auto_reference' => $this->auto_reference,
            'provider' => $this->provider,
        ];
    }
}
