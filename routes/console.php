<?php

use App\Console\Commands\UpdateAllStatusMetrics;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(UpdateAllStatusMetrics::class)
    ->everyFiveMinutes()
    ->name('status:update-all')
    ->withoutOverlapping()
    ->onFailure(function () {
        // Handle failure (e.g., send notification)
    });
