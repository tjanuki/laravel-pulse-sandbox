<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestApiConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if the API connection is working properly';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing API connection...');
        
        // Define the full URL for the test endpoint
        $apiUrl = config('app.url') . '/api/test';
        $this->line("Testing URL: {$apiUrl}");
        
        try {
            $response = Http::get($apiUrl);
            
            if ($response->successful()) {
                $this->info("✅ API connection is working!");
                $this->line("Response: " . json_encode($response->json(), JSON_PRETTY_PRINT));
                
                // Now test the status-metrics endpoint
                $this->info("\nNow testing the status-metrics endpoint...");
                $metricsUrl = config('app.url') . '/api/status-metrics';
                
                $testPayload = [
                    'source' => 'test',
                    'key' => 'connection',
                    'value' => 'success',
                    'status' => 'ok',
                ];
                
                $this->line("Testing URL: {$metricsUrl}");
                $response = Http::post($metricsUrl, $testPayload);
                
                if ($response->successful()) {
                    $this->info("✅ Status metrics API is working!");
                    $this->line("Response: " . json_encode($response->json(), JSON_PRETTY_PRINT));
                } else {
                    $this->error("❌ Status metrics API failed:");
                    $this->error("Status: " . $response->status());
                    $this->error("Response: " . $response->body());
                }
                
                return self::SUCCESS;
            } else {
                $this->error("❌ API connection failed:");
                $this->error("Status: " . $response->status());
                $this->error("Response: " . $response->body());
                
                $this->line("\nTroubleshooting suggestions:");
                $this->line("1. Check if your Laravel app is running");
                $this->line("2. Verify that your APP_URL in .env is correct: " . config('app.url'));
                $this->line("3. Check if Valet or your web server is properly configured");
                $this->line("4. Try to access the URL in a browser: {$apiUrl}");
                $this->line("5. Run 'php artisan route:list' to see all registered routes");
                $this->line("6. Clear route cache: 'php artisan route:clear'");
                
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("❌ Exception occurred: " . $e->getMessage());
            
            $this->line("\nTroubleshooting suggestions:");
            $this->line("1. Check if your Laravel app is running");
            $this->line("2. Verify that your APP_URL in .env is correct: " . config('app.url'));
            $this->line("3. Ensure you have the proper HTTP client installed: 'composer require guzzlehttp/guzzle'");
            
            return self::FAILURE;
        }
    }
}