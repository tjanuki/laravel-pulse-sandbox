<?php

/**
 * External Test Script for Status Metrics API
 *
 * This is a simple script to demonstrate how an external application
 * would send metrics to your Status Metrics API.
 *
 * Usage:
 * 1. Save this file as test-status-api.php
 * 2. Run: php test-status-api.php http://your-laravel-app.test/api/status-metrics
 */

if (!isset($argv[1])) {
    echo "Usage: php test-status-api.php [api-url]\n";
    echo "Example: php test-status-api.php http://localhost:8000/api/status-metrics\n";
    exit(1);
}

$apiUrl = $argv[1];
echo "Testing Status Metrics API at: {$apiUrl}\n\n";

// Simulated metrics from an external application
$metrics = [
    [
        'source' => 'external-service',
        'key' => 'health',
        'value' => '98',
        'status' => 'ok',
        'metadata' => [
            'response_time' => '120ms',
            'error_rate' => '0.1%',
            'uptime' => '99.99%'
        ]
    ],
    [
        'source' => 'external-service',
        'key' => 'response_time',
        'value' => '120',
        'status' => 'ok',
    ],
    [
        'source' => 'external-service',
        'key' => 'error_rate',
        'value' => '0.1',
        'status' => 'ok',
    ],
];

// Send each metric to the API
foreach ($metrics as $index => $metric) {
    echo "Sending metric " . ($index + 1) . "/" . count($metrics) . ": {$metric['source']} - {$metric['key']}\n";

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($metric));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "  Status Code: {$statusCode}\n";

    if ($statusCode >= 200 && $statusCode < 300) {
        echo "  ✓ Success!\n";
        $responseData = json_decode($response, true);
        echo "  Response: " . json_encode($responseData['message']) . "\n";
    } else {
        echo "  ✗ Failed!\n";
        echo "  Response: {$response}\n";
    }

    echo "\n";
}

echo "Test completed.\n";
echo "You can now check your Pulse dashboard to see the metrics from 'external-service'.\n";
