<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\StatusMetric;
use Illuminate\Console\Command;

class UpdateAllStatusMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:update-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all status metrics for the dashboard';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Updating all status metrics for the dashboard...');
        
        // Update blogs metrics
        $this->updateBlogsMetrics();
        
        // Update external service metrics
        $this->updateExternalServiceMetrics();
        
        $this->info('All status metrics updated successfully!');
        
        return self::SUCCESS;
    }
    
    /**
     * Update blogs metrics.
     */
    private function updateBlogsMetrics(): void
    {
        $this->info('Updating blogs metrics...');
        
        // Count blogs in last hour
        $hourlyCount = Blog::where('created_at', '>=', now()->subHour())->count();
        
        // Count blogs today
        $todayCount = Blog::whereDate('created_at', today())->count();
        
        // Total blogs
        $totalCount = Blog::count();
        
        // Determine status based on hourly count
        $status = $hourlyCount > 0 ? 'ok' : 'warning';
        
        // Create or update the main count metric
        StatusMetric::create([
            'source' => 'blogs',
            'key' => 'count',
            'value' => (string) $hourlyCount,
            'status' => $status,
            'metadata' => [
                'today_count' => $todayCount,
                'total_count' => $totalCount,
            ],
            'expires_at' => now()->addDays(60),
        ]);
        
        // Also update the original metrics
        StatusMetric::create([
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
        
        StatusMetric::create([
            'source' => 'blogs',
            'key' => 'today_count',
            'value' => (string) $todayCount,
            'status' => 'ok',
            'expires_at' => now()->addDays(60),
        ]);
        
        StatusMetric::create([
            'source' => 'blogs',
            'key' => 'total_count',
            'value' => (string) $totalCount,
            'status' => 'ok',
            'expires_at' => now()->addDays(60),
        ]);
        
        $this->info("✅ Updated blogs metrics: hourly_count={$hourlyCount}, status={$status}");
    }
    
    /**
     * Update external service metrics.
     */
    private function updateExternalServiceMetrics(): void
    {
        $this->info('Updating external service metrics...');
        
        // Simulate external service health (95% healthy)
        $healthValue = 95;
        
        // Simulate response time (120ms)
        $responseTime = 120;
        
        // Simulate error rate (0.1%)
        $errorRate = 0.1;
        
        // Create or update metrics
        StatusMetric::create([
            'source' => 'external-service',
            'key' => 'count',
            'value' => (string) $healthValue,
            'status' => 'ok',
            'metadata' => [
                'response_time' => $responseTime,
                'error_rate' => $errorRate,
            ],
            'expires_at' => now()->addDays(60),
        ]);
        
        StatusMetric::create([
            'source' => 'external-service',
            'key' => 'health',
            'value' => (string) $healthValue,
            'status' => 'ok',
            'metadata' => [
                'response_time' => $responseTime,
                'error_rate' => $errorRate,
            ],
            'expires_at' => now()->addDays(60),
        ]);
        
        StatusMetric::create([
            'source' => 'external-service',
            'key' => 'response_time',
            'value' => (string) $responseTime,
            'status' => 'ok',
            'expires_at' => now()->addDays(60),
        ]);
        
        StatusMetric::create([
            'source' => 'external-service',
            'key' => 'error_rate',
            'value' => (string) $errorRate,
            'status' => 'ok',
            'expires_at' => now()->addDays(60),
        ]);
        
        $this->info("✅ Updated external service metrics: health={$healthValue}, status=ok");
    }
}