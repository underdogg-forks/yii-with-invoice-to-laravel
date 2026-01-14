<?php

namespace Database\Seeders;

use App\Models\CustomField;
use Illuminate\Database\Seeder;

class CustomFieldSeeder extends Seeder
{
    public function run(): void
    {
        $customFields = [
            [
                'custom_field_table' => 'ip_clients',
                'custom_field_label' => 'Business Type',
                'custom_field_type' => 'select',
                'custom_field_order' => 1,
            ],
            [
                'custom_field_table' => 'ip_clients',
                'custom_field_label' => 'Internal Notes',
                'custom_field_type' => 'textarea',
                'custom_field_order' => 2,
            ],
            [
                'custom_field_table' => 'ip_clients',
                'custom_field_label' => 'Preferred Contact Method',
                'custom_field_type' => 'select',
                'custom_field_order' => 3,
            ],
            [
                'custom_field_table' => 'ip_clients',
                'custom_field_label' => 'Credit Limit',
                'custom_field_type' => 'number',
                'custom_field_order' => 4,
            ],
            [
                'custom_field_table' => 'ip_clients',
                'custom_field_label' => 'Newsletter Subscriber',
                'custom_field_type' => 'checkbox',
                'custom_field_order' => 5,
            ],
        ];

        foreach ($customFields as $field) {
            CustomField::create($field);
        }
    }
}
