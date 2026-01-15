<?php

return [
    'default' => [
        'requests' => 60,
        'period' => 60, // seconds
    ],
    
    'guest' => [
        'requests' => 30,
        'period' => 60,
    ],
    
    'authenticated' => [
        'requests' => 120,
        'period' => 60,
    ],
    
    'admin' => [
        'requests' => 300,
        'period' => 60,
    ],
    
    'api' => [
        'requests' => 1000,
        'period' => 3600, // per hour
    ],
    
    // Redis configuration
    'driver' => env('RATE_LIMIT_DRIVER', 'cache'), // cache or redis
    'prefix' => 'rate_limit:',
];
