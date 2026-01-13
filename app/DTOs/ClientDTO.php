<?php

namespace App\DTOs;

class ClientDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $client_name = null,
        public ?string $client_surname = null,
        public ?string $client_email = null,
        public ?string $client_phone = null,
        public ?string $client_mobile = null,
        public ?string $client_fax = null,
        public ?string $client_address_1 = null,
        public ?string $client_address_2 = null,
        public ?string $client_building_number = null,
        public ?string $client_city = null,
        public ?string $client_state = null,
        public ?string $client_zip = null,
        public ?string $client_country = null,
        public ?string $client_vat_id = null,
        public ?string $client_tax_code = null,
        public ?string $client_web = null,
        public ?string $client_number = null,
        public ?string $client_group = null,
        public ?string $client_frequency = null,
        public ?string $client_birthdate = null,
        public ?int $client_age = null,
        public ?string $client_gender = null,
        public ?string $client_title = null,
        public ?string $client_avs = null,
        public ?string $client_insured_number = null,
        public ?string $client_veka = null,
        public ?string $client_language = null,
        public bool $client_active = true,
    ) {}

    public static function fromModel($client): self
    {
        return new self(
            id: $client->id,
            client_name: $client->client_name,
            client_surname: $client->client_surname,
            client_email: $client->client_email,
            client_phone: $client->client_phone,
            client_mobile: $client->client_mobile,
            client_fax: $client->client_fax,
            client_address_1: $client->client_address_1,
            client_address_2: $client->client_address_2,
            client_building_number: $client->client_building_number,
            client_city: $client->client_city,
            client_state: $client->client_state,
            client_zip: $client->client_zip,
            client_country: $client->client_country,
            client_vat_id: $client->client_vat_id,
            client_tax_code: $client->client_tax_code,
            client_web: $client->client_web,
            client_number: $client->client_number,
            client_group: $client->client_group,
            client_frequency: $client->client_frequency,
            client_birthdate: $client->client_birthdate,
            client_age: $client->client_age,
            client_gender: $client->client_gender,
            client_title: $client->client_title,
            client_avs: $client->client_avs,
            client_insured_number: $client->client_insured_number,
            client_veka: $client->client_veka,
            client_language: $client->client_language,
            client_active: (bool) $client->client_active,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'client_name' => $this->client_name,
            'client_surname' => $this->client_surname,
            'client_email' => $this->client_email,
            'client_phone' => $this->client_phone,
            'client_mobile' => $this->client_mobile,
            'client_fax' => $this->client_fax,
            'client_address_1' => $this->client_address_1,
            'client_address_2' => $this->client_address_2,
            'client_building_number' => $this->client_building_number,
            'client_city' => $this->client_city,
            'client_state' => $this->client_state,
            'client_zip' => $this->client_zip,
            'client_country' => $this->client_country,
            'client_vat_id' => $this->client_vat_id,
            'client_tax_code' => $this->client_tax_code,
            'client_web' => $this->client_web,
            'client_number' => $this->client_number,
            'client_group' => $this->client_group,
            'client_frequency' => $this->client_frequency,
            'client_birthdate' => $this->client_birthdate,
            'client_age' => $this->client_age,
            'client_gender' => $this->client_gender,
            'client_title' => $this->client_title,
            'client_avs' => $this->client_avs,
            'client_insured_number' => $this->client_insured_number,
            'client_veka' => $this->client_veka,
            'client_language' => $this->client_language,
            'client_active' => $this->client_active,
        ], fn($value) => $value !== null);
    }
}
