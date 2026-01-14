<?php

return [
    'enabled' => env('TENANT_ENABLED', false),
    'mode' => env('TENANT_MODE', 'subdomain'), // subdomain, domain, path
    'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'app.test'),
    
    'database' => [
        'separate' => false, // Use separate databases per tenant
        'prefix' => '', // Table prefix for tenant tables
    ],
    
    'features' => [
        'auto_create_tenant' => false,
        'trial_days' => 14,
        'require_subscription' => true,
    ],
    
    // Cache tenant data for performance
    'cache_ttl' => 3600, // 1 hour
];
