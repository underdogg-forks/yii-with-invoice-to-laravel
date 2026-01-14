<?php

return [
    'headers' => [
        'X-Frame-Options' => 'DENY',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Content-Security-Policy' => env('CONTENT_SECURITY_POLICY', "default-src 'self'"),
        'Referrer-Policy' => 'no-referrer-when-downgrade',
    ],
    
    'sanitize' => [
        'enabled' => env('REQUEST_SANITIZE_ENABLED', true),
        'whitelist_fields' => ['description', 'notes', 'terms', 'content'],
        'allowed_tags' => '<p><br><strong><em><ul><ol><li><a><h1><h2><h3><h4><h5><h6>',
    ],
];
