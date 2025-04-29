<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\StatusMetric;
use Illuminate\Console\Command;

class TestDirectStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:direct-status-update {--force-alert : Force an alert state by sending 0 blogs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test creating status metrics directly without using the API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing direct status metrics update...');

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
        $this->info("Creating status metrics directly in the database...");

        try {
            // Create hourly count metric
            $metric = StatusMetric::create([
                'source' => 'blogs',
                'key' => 'hourly_count',
                'value' => (string) $hourlyCount,
                'status' => $status,
                'metadata' => [
                    'today_count' => $todayCount,
                    'total_count' => $totalCount,
                ],
                'expires_at' => now()->addDays(60),
            ]);
            
            $this->info("✅ Successfully created hourly count metric (ID: {$metric->id})");
            
            // Create today's count metric
            $metric = StatusMetric::create([
                'source' => 'blogs',
                'key' => 'today_count',
                'value' => (string) $todayCount,
                'status' => 'ok',
                'expires_at' => now()->addDays(60),
            ]);
            
            $this->info("✅ Successfully created today's count metric (ID: {$metric->id})");
            
            // Create total count metric
            $metric = StatusMetric::create([
                'source' => 'blogs',
                'key' => 'total_count',
                'value' => (string) $totalCount,
                'status' => 'ok',
                'expires_at' => now()->addDays(60),
            ]);
            
            $this->info("✅ Successfully created total count metric (ID: {$metric->id})");
            
            $this->newLine();
            $this->info("All metrics created successfully!");
            $this->info("You can now check the Pulse dashboard to see the results.");
            
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Exception occurred: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}