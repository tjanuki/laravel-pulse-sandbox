<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Metrics Service Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the metrics service.
    |
    */

    // API URL for sending metrics (must be an external endpoint)
    'api_url' => env('METRICS_API_URL', 'https://metrics.example.com/api/status-metrics'),
    
    // Default thresholds for various metrics
    'thresholds' => [
        'blogs' => [
            'hourly_count' => [
                'warning' => env('BLOGS_HOURLY_WARNING', 0),
                'error' => env('BLOGS_HOURLY_ERROR', null),
                'comparison' => 'less',
            ],
        ],
        'external-service' => [
            'health' => [
                'warning' => env('EXTERNAL_SERVICE_HEALTH_WARNING', 98),
                'error' => env('EXTERNAL_SERVICE_HEALTH_ERROR', 95),
                'comparison' => 'less',
            ],
            'response_time' => [
                'warning' => env('EXTERNAL_SERVICE_RESPONSE_TIME_WARNING', 250),
                'error' => env('EXTERNAL_SERVICE_RESPONSE_TIME_ERROR', 500),
                'comparison' => 'more',
            ],
            'error_rate' => [
                'warning' => env('EXTERNAL_SERVICE_ERROR_RATE_WARNING', 1),
                'error' => env('EXTERNAL_SERVICE_ERROR_RATE_ERROR', 5),
                'comparison' => 'more',
            ],
        ],
    ],
];