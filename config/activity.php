<?php

return [
    'enabled' => env('ACTIVITY_LOGGING_ENABLED', true),
    'retention_days' => env('ACTIVITY_RETENTION_DAYS', 90),
    'log_level' => env('ACTIVITY_LOG_LEVEL', 'info'),
    
    'exclude_urls' => [
        '/health',
        '/up',
        '/metrics',
        '/_debugbar',
        '/livewire/*',
    ],
    
    'exclude_actions' => [
        // 'view', // Uncomment to exclude read operations
    ],
    
    'sanitize_request' => true,
    'sanitize_fields' => ['password', 'password_confirmation', 'token', 'api_key', 'secret'],
    
    'log_response' => false, // Log response data (can be large)
    'log_request_body' => true, // Log request body
];
