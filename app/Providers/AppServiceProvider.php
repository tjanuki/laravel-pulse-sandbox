<?php

namespace App\Providers;

use App\Livewire\Pulse\StatusMonitor;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Pulse Custom Cards using Livewire component registration
        Livewire::component('pulse.status-monitor', StatusMonitor::class);

        // Keep any other component registrations if needed
        // Example: Livewire::component('pulse.queue-alert', QueueAlert::class);
    }
}
