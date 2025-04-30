<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Update all status metrics at once
        $schedule->command('status:update-all')->everyFiveMinutes();

        // Also keep the original reporters for backward compatibility
        $schedule->command('blogs:report-metrics')->everyFiveMinutes();

        // Run daily to clean up expired metrics
        $schedule->command('status:purge-old')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
