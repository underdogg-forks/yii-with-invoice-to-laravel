<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\CustomField;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample clients
        $clients = [
            [
                'client_name' => 'John',
                'client_surname' => 'Doe',
                'client_email' => 'john.doe@example.com',
                'client_phone' => '+31 20 123 4567',
                'client_mobile' => '+31 6 1234 5678',
                'client_address_1' => 'Dam Square 1',
                'client_city' => 'Amsterdam',
                'client_zip' => '1012 JS',
                'client_country' => 'Netherlands',
                'client_vat_id' => 'NL123456789B01',
                'client_active' => true,
                'client_language' => 'nl',
            ],
            [
                'client_name' => 'Jane',
                'client_surname' => 'Smith',
                'client_email' => 'jane.smith@example.com',
                'client_phone' => '+31 20 987 6543',
                'client_mobile' => '+31 6 9876 5432',
                'client_address_1' => 'Herengracht 100',
                'client_city' => 'Amsterdam',
                'client_zip' => '1015 BS',
                'client_country' => 'Netherlands',
                'client_vat_id' => 'NL987654321B01',
                'client_active' => true,
                'client_language' => 'en',
            ],
            [
                'client_name' => 'Acme',
                'client_surname' => 'Corporation',
                'client_email' => 'info@acme.com',
                'client_phone' => '+31 30 555 1234',
                'client_address_1' => 'Business Park 50',
                'client_city' => 'Utrecht',
                'client_zip' => '3542 AD',
                'client_country' => 'Netherlands',
                'client_vat_id' => 'NL555123456B01',
                'client_active' => true,
                'client_group' => 'corporate',
                'client_language' => 'en',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}
