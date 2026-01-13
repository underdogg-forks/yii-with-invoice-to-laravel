<?php

namespace App\DTOs;

class RegisterDTO
{
    public function __construct(
        public string $login,
        public string $email,
        public string $password,
        public string $password_confirmation,
    ) {}

    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
