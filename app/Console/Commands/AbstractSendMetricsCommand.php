<?php

namespace App\Console\Commands;

use App\Services\MetricsService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

abstract class AbstractSendMetricsCommand extends Command
{
    /**
     * The metrics service.
     *
     * @var MetricsService
     */
    protected MetricsService $metricsService;

    /**
     * Define the metrics source identifier.
     * Should be overridden by child classes.
     *
     * @return string
     */
    abstract protected function getMetricsSource(): string;
    
    /**
     * Get the metrics data to send.
     * Should be overridden by child classes.
     *
     * @return array|Collection
     */
    abstract protected function getMetricsData();

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Get the API URL from configuration
        $apiUrl = config('services.metrics.api_url');
        
        // Create the metrics service with the appropriate source
        $this->metricsService = new MetricsService($this->getMetricsSource(), $apiUrl);
        
        // Get the metrics data
        $metrics = $this->getMetricsData();
        
        // Convert Collection to array if necessary
        if ($metrics instanceof Collection) {
            $metrics = $metrics->toArray();
        }
        
        // Track success status
        $success = true;
        $totalMetrics = count($metrics);
        
        // Create a progress bar for multiple metrics
        if ($totalMetrics > 1) {
            $bar = $this->output->createProgressBar($totalMetrics);
            $bar->start();
        }
        
        // Send each metric
        foreach ($metrics as $index => $metric) {
            $metricKey = $metric['key'];
            
            // Log if verbose
            if ($this->option('verbose')) {
                $this->info("Sending metric {$metricKey}");
            }
            
            // Send the metric
            $response = $this->metricsService->sendMetric(
                $metricKey,
                $metric['value'],
                $metric['status'] ?? 'ok',
                $metric['metadata'] ?? []
            );
            
            // Check response
            if (!$response->successful()) {
                $this->error("Failed to send metric {$metricKey}: {$response->status()}");
                $success = false;
            } elseif ($this->option('verbose')) {
                $this->info("Successfully sent metric {$metricKey}");
            }
            
            // Advance progress bar
            if ($totalMetrics > 1) {
                $bar->advance();
            }
        }
        
        // Finish progress bar
        if ($totalMetrics > 1) {
            $bar->finish();
            $this->newLine();
        }
        
        // Output final status
        if ($success) {
            $this->info("All metrics sent successfully");
            return self::SUCCESS;
        } else {
            $this->error("Failed to send some metrics");
            return self::FAILURE;
        }
    }

    /**
     * Get the command name for use in the signature.
     * Default implementation returns the metrics source, but can be overridden.
     *
     * @return string
     */
    protected function getCommandName(): string
    {
        return $this->getMetricsSource();
    }
}