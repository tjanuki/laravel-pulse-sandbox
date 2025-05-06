<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class MetricsService
{
    /**
     * Metric timing constants
     */
    public const TIMING_FIVE_MINUTES = 'five_minutes';
    public const TIMING_HOURLY = 'hourly';
    public const TIMING_DAILY = 'daily';
    public const TIMING_TOTAL = 'total';

    /**
     * @var string The API URL for sending metrics
     */
    protected string $apiUrl;

    /**
     * @var string The source of the metrics
     */
    protected string $source;

    /**
     * Create a new metrics service instance.
     *
     * @param string $source The source of the metrics
     * @param string|null $apiUrl Optional custom API URL (for testing only)
     */
    public function __construct(string $source, ?string $apiUrl = null)
    {
        $this->source = $source;
        $this->apiUrl = $apiUrl ?? config('metrics.api_url');

        if (empty($this->apiUrl)) {
            throw new \InvalidArgumentException('Metrics API URL must be configured in metrics.api_url');
        }
    }

    /**
     * Send a metric to the API.
     *
     * @param string $key The metric key
     * @param string|int|float $value The metric value
     * @param string $status The metric status ('ok', 'warning', 'error')
     * @param array $metadata Additional metadata
     * @return Response
     */
    public function sendMetric(string $key, $value, string $status = 'ok', array $metadata = []): Response
    {
        return Http::acceptJson()
            ->contentType('application/json')
            ->post($this->apiUrl, [
                'source' => $this->source,
                'key' => $key,
                'value' => (string) $value,
                'status' => $status,
                'metadata' => $metadata,
            ]);
    }

    /**
     * Send multiple metrics to the API.
     *
     * @param array $metrics Array of metrics to send
     * @return array Array of responses
     */
    public function sendMetrics(array $metrics): array
    {
        $responses = [];

        foreach ($metrics as $metric) {
            $key = $metric['key'];
            $value = $metric['value'];
            $status = $metric['status'] ?? 'ok';
            $metadata = $metric['metadata'] ?? [];

            $responses[$key] = $this->sendMetric($key, $value, $status, $metadata);
        }

        return $responses;
    }

    /**
     * Determine status based on a threshold.
     *
     * @param mixed $value The value to check
     * @param mixed $warningThreshold The threshold for warning status
     * @param mixed $errorThreshold The threshold for error status
     * @param string $comparison The comparison type ('less', 'more')
     * @return string The status ('ok', 'warning', 'error')
     */
    public function determineStatus($value, $warningThreshold, $errorThreshold = null, string $comparison = 'less'): string
    {
        // Default error threshold to warning threshold if not provided
        $errorThreshold = $errorThreshold ?? $warningThreshold;

        if ($comparison === 'less') {
            if ($value <= $errorThreshold) {
                return 'error';
            }

            if ($value <= $warningThreshold) {
                return 'warning';
            }

            return 'ok';
        } else {
            if ($value >= $errorThreshold) {
                return 'error';
            }

            if ($value >= $warningThreshold) {
                return 'warning';
            }

            return 'ok';
        }
    }
}
