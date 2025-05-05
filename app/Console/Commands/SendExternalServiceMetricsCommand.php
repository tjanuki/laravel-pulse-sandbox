<?php

namespace App\Console\Commands;

use App\Services\MetricsService;

class SendExternalServiceMetricsCommand extends AbstractSendMetricsCommand
{
    /**
     * Metrics source constant.
     *
     * @var string
     */
    public const METRICS_SOURCE = 'external-service';
    
    /**
     * Metrics keys.
     */
    public const METRICS_KEY_HEALTH = 'health';
    public const METRICS_KEY_RESPONSE_TIME = 'response_time';
    public const METRICS_KEY_ERROR_RATE = 'error_rate';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-metrics:external-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send external service metrics to the status API';

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
        // Simulated metrics
        return [
            [
                'key' => self::METRICS_KEY_HEALTH,
                'value' => rand(98, 100),
                'status' => 'ok',
                'metadata' => [
                    'timing' => MetricsService::TIMING_FIVE_MINUTES,
                    'response_time' => '120ms',
                    'error_rate' => '0.1%',
                    'uptime' => '99.99%'
                ]
            ],
            [
                'key' => self::METRICS_KEY_RESPONSE_TIME,
                'value' => rand(100, 200),
                'status' => 'ok',
                'metadata' => [
                    'timing' => MetricsService::TIMING_FIVE_MINUTES,
                ]
            ],
            [
                'key' => self::METRICS_KEY_ERROR_RATE,
                'value' => '0.1',
                'status' => 'ok',
                'metadata' => [
                    'timing' => MetricsService::TIMING_FIVE_MINUTES,
                ]
            ],
        ];
    }
}