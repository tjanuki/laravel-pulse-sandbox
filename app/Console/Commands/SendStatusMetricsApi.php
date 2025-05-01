<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendStatusMetricsApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:status-metrics-api {api_url? : The URL of the status metrics API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send metrics to the Status Metrics API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get the API URL from the argument or use a default URL
        $apiUrl = $this->argument('api_url') ?? config('app.url') . '/api/status-metrics';

        $this->info("Sending metrics to Status API at: {$apiUrl}");
        $this->newLine();

        // Simulated metrics from an external application
        $metrics = [
            [
                'source' => 'external-service',
                'key' => 'health',
                'value' => (string) rand(98, 100),
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
                'value' => (string) rand(100, 200),
                'status' => 'ok',
            ],
            [
                'source' => 'external-service',
                'key' => 'error_rate',
                'value' => '0.1',
                'status' => 'ok',
            ],
        ];

        // Create a progress bar
        $bar = $this->output->createProgressBar(count($metrics));
        $bar->start();

        // Send each metric to the API
        foreach ($metrics as $index => $metric) {
            $this->info("Sending metric " . ($index + 1) . "/" . count($metrics) . ": {$metric['source']} - {$metric['key']}");

            // Use Laravel's HTTP client instead of curl
            $response = Http::acceptJson()
                ->contentType('application/json')
                ->post($apiUrl, $metric);

            $statusCode = $response->status();

            $this->info("  Status Code: {$statusCode}");

            if ($response->successful()) {
                $this->info("  ✓ Success!");
                $this->info("  Response: " . json_encode($response->json()['message']));
            } else {
                $this->error("  ✗ Failed!");
                $this->error("  Response: " . $response->body());
            }

            $this->newLine();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("All metrics sent successfully.");
        $this->info("You can now check your Pulse dashboard to see the metrics from 'external-service'.");

        return self::SUCCESS;
    }
}
