<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestStatusMetricsApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:status-metrics-api {--force-alert : Force an alert state by sending 0 blogs} {--debug : Show full request and response details}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending blog metrics to the status-metrics API endpoint';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing the status-metrics API endpoint...');

        // Calculate blog counts
        $hourlyCount = $this->option('force-alert') ? 0 : Blog::where('created_at', '>=', now()->subHour())->count();
        $todayCount = Blog::whereDate('created_at', today())->count();
        $totalCount = Blog::count();

        // Determine status based on hourly count
        $status = $hourlyCount > 0 ? 'ok' : 'warning';

        $this->info("Current blog metrics:");
        $this->table(
            ['Metric', 'Value', 'Status'],
            [
                ['Hourly Count', $hourlyCount, $status],
                ['Today\'s Count', $todayCount, 'ok'],
                ['Total Count', $totalCount, 'ok'],
            ]
        );

        $this->newLine();
        $this->info("Sending metrics to API...");

        // Define the full URL for the API endpoint
        $apiUrl = config('app.url') . '/api/status-metrics';

        // If debug option is set, show the URL
        if ($this->option('debug')) {
            $this->line("API URL: {$apiUrl}");
        }

        // Send hourly count to API
        try {
            $payload = [
                'source' => 'blogs',
                'key' => 'hourly_count',
                'value' => (string) $hourlyCount,
                'status' => $status,
                'metadata' => [
                    'today_count' => $todayCount,
                    'total_count' => $totalCount,
                ],
            ];

            // If debug option is set, show the payload
            if ($this->option('debug')) {
                $this->line("Request payload: " . json_encode($payload, JSON_PRETTY_PRINT));
            }

            $response = Http::post($apiUrl, $payload);

            if ($response->successful()) {
                $this->info("✓ Successfully sent hourly count metric");
                $this->line("  Response: " . json_encode($response->json(), JSON_PRETTY_PRINT));
            } else {
                $this->error("✗ Failed to send hourly count metric");
                $this->error("  Status: " . $response->status());
                $this->error("  Response: " . $response->body());

                // Provide more detailed troubleshooting suggestions
                $this->line("\nTroubleshooting suggestions:");
                $this->line("1. Check that your API routes are registered in routes/api.php");
                $this->line("2. Verify your APP_URL is set correctly in .env file");
                $this->line("3. Try testing the route directly in a browser: {$apiUrl}");
                $this->line("4. Make sure you've registered the StatusMetricController");
                $this->line("5. Run 'php artisan route:list | grep status-metrics' to check if the route exists");

                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception occurred: " . $e->getMessage());
            return self::FAILURE;
        }

        // Rest of the code remains the same...
        // Send today's count
        try {
            $response = Http::post($apiUrl, [
                'source' => 'blogs',
                'key' => 'today_count',
                'value' => (string) $todayCount,
                'status' => 'ok',
            ]);

            if ($response->successful()) {
                $this->info("✓ Successfully sent today's count metric");
            } else {
                $this->error("✗ Failed to send today's count metric");
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception occurred: " . $e->getMessage());
            return self::FAILURE;
        }

        // Send total count
        try {
            $response = Http::post($apiUrl, [
                'source' => 'blogs',
                'key' => 'total_count',
                'value' => (string) $totalCount,
                'status' => 'ok',
            ]);

            if ($response->successful()) {
                $this->info("✓ Successfully sent total count metric");
            } else {
                $this->error("✗ Failed to send total count metric");
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("✗ Exception occurred: " . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info("All metrics sent successfully!");
        $this->info("You can now check the Pulse dashboard to see the results.");

        return self::SUCCESS;
    }
}
