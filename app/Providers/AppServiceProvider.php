<?php

namespace App\Providers;

use App\Livewire\Pulse\BlogsMonitor;
use App\Livewire\Pulse\QueueAlert;
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
        Livewire::component('pulse.queue-alert', QueueAlert::class);
        Livewire::component('pulse.blogs-monitor', BlogsMonitor::class);
    }
}
