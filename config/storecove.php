<?php

return [
    /*
    |--------------------------------------------------------------------------
    | StoreCove API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for StoreCove Peppol service provider integration.
    | StoreCove is used to send invoices via the Peppol network.
    |
    */

    'api_key' => env('STORECOVE_API_KEY', ''),

    'api_url' => env('STORECOVE_API_URL', 'https://api.storecove.com/api/v2'),

    'webhook_url' => env('STORECOVE_WEBHOOK_URL', ''),

    'timeout' => env('STORECOVE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Specify whether to use sandbox or production environment.
    | Available values: 'sandbox', 'production'
    |
    */

    'environment' => env('STORECOVE_ENVIRONMENT', 'sandbox'),
];
