<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $login = null,
        public ?string $email = null,
        public ?string $password = null,
        public bool $tfa_enabled = false,
        public ?string $totp_secret = null,
    ) {}

    public static function fromModel($user): self
    {
        return new self(
            id: $user->id,
            login: $user->login,
            email: $user->email,
            tfa_enabled: $user->tfa_enabled ?? false,
            totp_secret: $user->totp_secret,
        );
    }

    public function toArray(): array
    {
        $data = [
            'login' => $this->login,
            'email' => $this->email,
            'tfa_enabled' => $this->tfa_enabled,
        ];

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        if ($this->totp_secret !== null) {
            $data['totp_secret'] = $this->totp_secret;
        }

        return $data;
    }
}
