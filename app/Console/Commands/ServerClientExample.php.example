<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ServerClientExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'metrics:send-example
                            {--api-url=http://localhost/api/status-metrics : The URL of the metrics API}
                            {--api-key= : The API key for server authentication}
                            {--source=app : The metrics source}
                            {--server-name= : The server name (defaults to hostname)}
                            {--environment=production : The environment}
                            {--region= : The region or datacenter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example client to send metrics to a central Pulse server';

    /**
     * Metric types to collect and send.
     */
    protected array $metricTypes = [
        'cpu_usage',
        'memory_usage',
        'disk_usage',
        'response_time',
        'error_rate',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get command options
        $apiUrl = $this->option('api-url');
        $apiKey = $this->option('api-key');
        $source = $this->option('source');
        $serverName = $this->option('server-name') ?: gethostname();
        $environment = $this->option('environment');
        $region = $this->option('region');

        // Validate API key
        if (empty($apiKey)) {
            $this->error('API key is required. Use --api-key option.');
            return self::FAILURE;
        }

        $this->info("Collecting metrics from {$serverName}...");

        // Collect system metrics
        $metrics = $this->collectMetrics();

        $this->info("Sending " . count($metrics) . " metrics to {$apiUrl}...");

        // Create a progress bar
        $bar = $this->output->createProgressBar(count($metrics));
        $bar->start();

        $success = true;

        // Send each metric
        foreach ($metrics as $metric) {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
            ])->post($apiUrl, [
                'source' => $source,
                'server_name' => $serverName,
                'environment' => $environment,
                'region' => $region,
                'key' => $metric['key'],
                'value' => $metric['value'],
                'status' => $metric['status'],
                'metadata' => $metric['metadata'],
            ]);

            if (!$response->successful()) {
                $this->newLine();
                $this->error("Failed to send metric {$metric['key']}: " . $response->status() . " " . $response->body());
                $success = false;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($success) {
            $this->info("All metrics sent successfully!");
            return self::SUCCESS;
        } else {
            $this->error("Some metrics failed to send. Check the output above for details.");
            return self::FAILURE;
        }
    }

    /**
     * Collect system metrics.
     * This is a simulated example - in a real implementation, you would
     * collect actual system metrics.
     */
    protected function collectMetrics(): array
    {
        $metrics = [];

        // CPU Usage (simulated)
        $cpuUsage = mt_rand(10, 95); // percentage
        $cpuStatus = $this->determineStatus($cpuUsage, 80, 90);
        $metrics[] = [
            'key' => 'cpu_usage',
            'value' => (string) $cpuUsage,
            'status' => $cpuStatus,
            'metadata' => [
                'cores' => 4,
                'type' => 'system',
            ],
        ];

        // Memory Usage (simulated)
        $memoryUsage = mt_rand(20, 90); // percentage
        $memoryStatus = $this->determineStatus($memoryUsage, 75, 85);
        $metrics[] = [
            'key' => 'memory_usage',
            'value' => (string) $memoryUsage,
            'status' => $memoryStatus,
            'metadata' => [
                'total' => '16GB',
                'type' => 'system',
            ],
        ];

        // Disk Usage (simulated)
        $diskUsage = mt_rand(30, 95); // percentage
        $diskStatus = $this->determineStatus($diskUsage, 80, 90);
        $metrics[] = [
            'key' => 'disk_usage',
            'value' => (string) $diskUsage,
            'status' => $diskStatus,
            'metadata' => [
                'total' => '500GB',
                'type' => 'system',
            ],
        ];

        // Response Time (simulated)
        $responseTime = mt_rand(50, 500); // ms
        $responseTimeStatus = $this->determineStatus($responseTime, 200, 400);
        $metrics[] = [
            'key' => 'response_time',
            'value' => (string) $responseTime,
            'status' => $responseTimeStatus,
            'metadata' => [
                'endpoint' => '/api',
                'type' => 'application',
            ],
        ];

        // Error Rate (simulated)
        $errorRate = mt_rand(0, 10) / 10; // 0% to 1.0%
        $errorRateStatus = $this->determineStatus($errorRate, 0.5, 1.0);
        $metrics[] = [
            'key' => 'error_rate',
            'value' => (string) $errorRate,
            'status' => $errorRateStatus,
            'metadata' => [
                'period' => '5min',
                'type' => 'application',
            ],
        ];

        return $metrics;
    }

    /**
     * Determine status based on thresholds.
     */
    protected function determineStatus($value, $warningThreshold, $criticalThreshold): string
    {
        if ($value >= $criticalThreshold) {
            return 'critical';
        } elseif ($value >= $warningThreshold) {
            return 'warning';
        } else {
            return 'ok';
        }
    }
}