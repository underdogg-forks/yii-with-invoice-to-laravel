<?php

return [
    'default_version' => env('API_DEFAULT_VERSION', '1'),
    'latest_version' => '1',
    
    'versions' => [
        '1' => [
            'status' => 'stable',
            'deprecation_date' => null,
            'sunset_date' => null,
        ],
    ],
    
    'header_name' => 'Accept',
    'header_pattern' => '/application\/vnd\.api\+json;\s*version=(\d+)/',
    
    'deprecation_warning' => true,
];
