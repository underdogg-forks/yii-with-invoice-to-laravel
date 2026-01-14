<?php

return [
    'api_provider' => env('CURRENCY_API_PROVIDER', 'fixer'),
    'api_key' => env('CURRENCY_API_KEY'),
    'base_currency' => env('CURRENCY_BASE', 'EUR'),
    'cache_ttl' => 86400, // 24 hours
    
    'supported_currencies' => [
        'EUR', 'USD', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'NZD',
        'SEK', 'NOK', 'DKK', 'PLN', 'CZK', 'HUF', 'RON', 'BGN',
    ],
    
    'format' => [
        'EUR' => ['symbol' => '€', 'decimals' => 2, 'position' => 'after'],
        'USD' => ['symbol' => '$', 'decimals' => 2, 'position' => 'before'],
        'GBP' => ['symbol' => '£', 'decimals' => 2, 'position' => 'before'],
        'JPY' => ['symbol' => '¥', 'decimals' => 0, 'position' => 'before'],
        'CHF' => ['symbol' => 'CHF', 'decimals' => 2, 'position' => 'after'],
        'CAD' => ['symbol' => 'CA$', 'decimals' => 2, 'position' => 'before'],
        'AUD' => ['symbol' => 'A$', 'decimals' => 2, 'position' => 'before'],
        'NZD' => ['symbol' => 'NZ$', 'decimals' => 2, 'position' => 'before'],
    ],
];
