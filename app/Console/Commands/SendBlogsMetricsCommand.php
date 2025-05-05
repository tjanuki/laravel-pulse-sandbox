<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Services\MetricsService;

class SendBlogsMetricsCommand extends AbstractSendMetricsCommand
{
    /**
     * Metrics source constant.
     *
     * @var string
     */
    public const METRICS_SOURCE = 'blogs';
    
    /**
     * Metrics keys.
     */
    public const METRICS_KEY_HOURLY = 'hourly_count';
    public const METRICS_KEY_DAILY = 'today_count';
    public const METRICS_KEY_TOTAL = 'total_count';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-metrics:blogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send blog metrics to the status API';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define the metrics source identifier.
     *
     * @return string
     */
    protected function getMetricsSource(): string
    {
        return self::METRICS_SOURCE;
    }

    /**
     * Get the metrics data to send.
     *
     * @return array
     */
    protected function getMetricsData(): array
    {
        // Count blogs in last hour
        $hourlyCount = Blog::where('created_at', '>=', now()->subHour())->count();
        
        // Count blogs today
        $todayCount = Blog::whereDate('created_at', today())->count();
        
        // Total blogs
        $totalCount = Blog::count();
        
        // Determine status based on hourly count
        $status = $hourlyCount > 0 ? 'ok' : 'warning';
        
        return [
            [
                'key' => self::METRICS_KEY_HOURLY,
                'value' => $hourlyCount,
                'status' => $status,
                'metadata' => [
                    'timing' => MetricsService::TIMING_HOURLY,
                    'today_count' => $todayCount,
                    'total_count' => $totalCount,
                ],
            ],
            [
                'key' => self::METRICS_KEY_DAILY,
                'value' => $todayCount,
                'status' => 'ok',
                'metadata' => [
                    'timing' => MetricsService::TIMING_DAILY,
                ],
            ],
            [
                'key' => self::METRICS_KEY_TOTAL,
                'value' => $totalCount,
                'status' => 'ok',
                'metadata' => [
                    'timing' => MetricsService::TIMING_TOTAL,
                ],
            ],
        ];
    }
}