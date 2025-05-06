<?php

namespace App\Providers;

use App\Services\MetricsService;
use Illuminate\Support\ServiceProvider;

class MetricsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/metrics.php', 'services.metrics'
        );

        $this->app->singleton(MetricsService::class, function ($app) {
            return new MetricsService(
                config('metrics.default_source', 'application'),
                config('metrics.api_url')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/metrics.php' => config_path('services/metrics.php'),
        ], 'config');

        // Register commands only when running in console
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SendBlogsMetricsCommand::class,
                \App\Console\Commands\SendExternalServiceMetricsCommand::class,
            ]);
        }
    }
}
