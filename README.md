# Metrics Service for Laravel

This package provides a reusable metrics service and command structure for Laravel applications. It allows sending metrics to a status API endpoint from different sources.

## Requirements

- PHP 8.1+
- Laravel 9+

## Installation

1. Copy the service and command files to your Laravel project.
2. Register the `MetricsServiceProvider` in your `config/app.php` file:

```php
'providers' => [
    // Other Service Providers...
    App\Providers\MetricsServiceProvider::class,
],
```

3. Publish the config file:

```bash
php artisan vendor:publish --provider="App\Providers\MetricsServiceProvider" --tag="config"
```

## Configuration

After publishing the config file, you can set your metrics configurations in `config/services/metrics.php` or through environment variables:

```
# Required - this must be set to an external API endpoint
METRICS_API_URL=https://your-metrics-api-endpoint.com/api/status-metrics

# Optional threshold configurations
BLOGS_HOURLY_WARNING=0
EXTERNAL_SERVICE_HEALTH_WARNING=98
EXTERNAL_SERVICE_HEALTH_ERROR=95
```

**Important**: The `METRICS_API_URL` must be set to an external API endpoint and is a required configuration.

## Available Commands

### Send Blog Metrics

Sends blog-related metrics to the status API:

```bash
php artisan send-metrics:blogs
```

### Send External Service Metrics

Sends simulated external service metrics to the status API:

```bash
php artisan send-metrics:external-service
```

The API endpoint is configured solely through the configuration file or environment variables and cannot be overridden via command-line arguments.

```bash
# Example of running the commands
php artisan send-metrics:blogs
php artisan send-metrics:external-service
```

## Creating Your Own Metrics Command

To create a new metrics command, extend the `AbstractSendMetricsCommand` class:

```php
<?php

namespace App\Console\Commands;

use App\Services\MetricsService;

class SendMyServiceMetricsCommand extends AbstractSendMetricsCommand
{
    public const METRICS_SOURCE = 'my-service';
    
    public const METRICS_KEY_STATUS = 'status';
    public const METRICS_KEY_PERFORMANCE = 'performance';
    
    protected $signature = 'send-metrics:my-service';
    protected $description = 'Send my service metrics to the status API';

    public function __construct()
    {
        parent::__construct();
    }

    protected function getMetricsSource(): string
    {
        return self::METRICS_SOURCE;
    }

    protected function getMetricsData(): array
    {
        // Collect your metrics data here
        return [
            [
                'key' => self::METRICS_KEY_STATUS,
                'value' => 'ok',
                'status' => 'ok',
                'metadata' => [
                    'timing' => MetricsService::TIMING_FIVE_MINUTES,
                ],
            ],
            // Add more metrics...
        ];
    }
}
```

Then register your command in the `MetricsServiceProvider::boot()` method.

## Using the Metrics Service Directly

You can also use the `MetricsService` class directly for more customized metrics reporting:

```php
$metricsService = new MetricsService('my-custom-source');
$response = $metricsService->sendMetric('custom_metric', $value, 'ok', [
    'additional' => 'metadata',
]);
```

## Timing Constants

The `MetricsService` class provides several timing constants for common reporting intervals:

- `MetricsService::TIMING_FIVE_MINUTES`
- `MetricsService::TIMING_HOURLY`
- `MetricsService::TIMING_DAILY`
- `MetricsService::TIMING_TOTAL`

Use these constants in your metrics metadata to indicate their reporting frequency.