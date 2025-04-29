<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ReportBlogMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blogs:report-metrics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report blog metrics to the status API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Count blogs in last hour
        $hourlyCount = Blog::where('created_at', '>=', now()->subHour())->count();
        
        // Count blogs today
        $todayCount = Blog::whereDate('created_at', today())->count();
        
        // Total blogs
        $totalCount = Blog::count();
        
        // Determine status based on hourly count
        $status = $hourlyCount > 0 ? 'ok' : 'warning';
        
        // Send hourly count to API
        $response = Http::post(url('/api/status-metrics'), [
            'source' => 'blogs',
            'key' => 'hourly_count',
            'value' => (string) $hourlyCount,
            'status' => $status,
            'metadata' => [
                'today_count' => $todayCount,
                'total_count' => $totalCount,
            ],
        ]);
        
        // Also report additional metrics separately
        $todayResponse = Http::post(url('/api/status-metrics'), [
            'source' => 'blogs',
            'key' => 'today_count',
            'value' => (string) $todayCount,
            'status' => 'ok',
        ]);
        
        $totalResponse = Http::post(url('/api/status-metrics'), [
            'source' => 'blogs',
            'key' => 'total_count',
            'value' => (string) $totalCount,
            'status' => 'ok',
        ]);
        
        if ($response->successful() && $todayResponse->successful() && $totalResponse->successful()) {
            $this->info("Blog metrics reported successfully");
            return self::SUCCESS;
        } else {
            $this->error("Failed to report blog metrics");
            return self::FAILURE;
        }
    }
}