<?php

namespace App\Livewire\Pulse;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class QueueAlert extends Card
{
    const QUEUE_BACK_LOGGED_LIMIT = 5;

    /**
     * Render the component.
     */
    public function render()
    {
        // Query queue data directly from Laravel's queue tables, not from Pulse
        $queuedJobs = $this->getQueuedJobsCount();
        $processingJobs = $this->getProcessingJobsCount();
        $failedJobs = $this->getFailedJobsCount();
        $longRunningJobs = $this->getLongRunningJobsCount();

        // Get details of long-running jobs if any
        $longRunningJobDetails = $longRunningJobs > 0 ? $this->getLongRunningJobDetails() : [];

        // Define alert thresholds - these can be customized
        $isQueueBacklogged = $queuedJobs > self::QUEUE_BACK_LOGGED_LIMIT;        // Alert if more than 50 jobs queued
        $hasTooManyProcessing = $processingJobs > 10; // Alert if more than 10 jobs processing
        $hasLongRunningJobs = $longRunningJobs > 0;   // Alert if any jobs running longer than 60s
        $hasRecentFailures = $failedJobs > 0;         // Alert if any jobs failed recently

        // Check if any alerts are active
        $hasAlerts = $isQueueBacklogged || $hasTooManyProcessing || $hasLongRunningJobs || $hasRecentFailures;

        return view('livewire.pulse.queue-alert', [
            'queuedJobs' => $queuedJobs,
            'processingJobs' => $processingJobs,
            'longRunningJobs' => $longRunningJobs,
            'failedJobsLastHour' => $failedJobs,
            'isQueueBacklogged' => $isQueueBacklogged,
            'hasTooManyProcessing' => $hasTooManyProcessing,
            'hasLongRunningJobs' => $hasLongRunningJobs,
            'hasRecentFailures' => $hasRecentFailures,
            'hasAlerts' => $hasAlerts,
            'longRunningJobDetails' => $longRunningJobDetails,
        ]);
    }

    /**
     * Get the count of queued jobs
     */
    private function getQueuedJobsCount()
    {
        if (!Schema::hasTable('jobs')) {
            return 0;
        }

        return DB::table('jobs')
            ->whereNull('reserved_at')
            ->count();
    }

    /**
     * Get the count of processing jobs
     */
    private function getProcessingJobsCount()
    {
        if (!Schema::hasTable('jobs')) {
            return 0;
        }

        return DB::table('jobs')
            ->whereNotNull('reserved_at')
            ->count();
    }

    /**
     * Get the count of failed jobs
     */
    private function getFailedJobsCount()
    {
        if (!Schema::hasTable('failed_jobs')) {
            return 0;
        }

        return DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subHour())
            ->count();
    }

    /**
     * Get the count of long-running jobs (running for more than 60 seconds)
     */
    private function getLongRunningJobsCount()
    {
        if (!Schema::hasTable('jobs')) {
            return 0;
        }

        return DB::table('jobs')
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '<=', now()->subSeconds(60))
            ->count();
    }

    /**
     * Get details of long-running jobs
     */
    private function getLongRunningJobDetails()
    {
        if (!Schema::hasTable('jobs')) {
            return [];
        }

        $longRunningJobs = DB::table('jobs')
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '<=', now()->subSeconds(60))
            ->orderBy('reserved_at', 'asc')
            ->limit(5)
            ->get();

        return $longRunningJobs->map(function ($job) {
            // Extract job name from payload
            $payload = json_decode($job->payload, true);
            $commandName = $payload['data']['commandName'] ?? 'Unknown';

            // Calculate runtime in seconds
            $runtime = now()->diffInSeconds(\Carbon\Carbon::createFromTimestamp($job->reserved_at));

            return (object) [
                'name' => $commandName,
                'runtime' => $runtime,
                'queue' => $job->queue,
            ];
        });
    }
}
