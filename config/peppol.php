<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Peppol Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Peppol BIS 3.0 compliant UBL XML generation
    |
    */

    'supplier' => [
        // Supplier endpoint ID (electronic address)
        'endpoint_id' => env('PEPPOL_SUPPLIER_ENDPOINT_ID', ''),
        
        // Scheme ID for endpoint (0088 = GLN, 0190 = Dutch Chamber of Commerce, etc.)
        'scheme_id' => env('PEPPOL_SUPPLIER_SCHEME_ID', '0088'),
        
        // VAT number
        'vat_number' => env('PEPPOL_SUPPLIER_VAT_NUMBER', ''),
        
        // Address
        'address' => [
            'street' => env('PEPPOL_SUPPLIER_STREET', ''),
            'city' => env('PEPPOL_SUPPLIER_CITY', ''),
            'postal_code' => env('PEPPOL_SUPPLIER_POSTAL_CODE', ''),
            'country_code' => env('PEPPOL_SUPPLIER_COUNTRY', 'NL'),
        ],
    ],

    // Peppol network service providers
    'service_providers' => [
        'storecove' => [
            'enabled' => env('PEPPOL_STORECOVE_ENABLED', false),
            'api_key' => env('PEPPOL_STORECOVE_API_KEY', ''),
            'endpoint' => env('PEPPOL_STORECOVE_ENDPOINT', 'https://api.storecove.com/api/v2'),
        ],
    ],

    // Default tax rates
    'tax' => [
        'default_rate' => env('PEPPOL_DEFAULT_TAX_RATE', 21.00),
        'default_scheme' => 'VAT',
    ],

    // Currency
    'default_currency' => env('PEPPOL_DEFAULT_CURRENCY', 'EUR'),
];
