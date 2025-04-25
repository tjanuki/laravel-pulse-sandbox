<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LongRunningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job should run.
     */
    protected int $runningTime;

    /**
     * Create a new job instance.
     */
    public function __construct(int $runningTime = 30)
    {
        $this->runningTime = $runningTime;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting long running job that will take {$this->runningTime} seconds");

        $startTime = now();

        // Log progress periodically
        for ($i = 0; $i < $this->runningTime; $i++) {
            if ($i % 5 == 0) {
                Log::info("Long running job in progress: {$i} seconds elapsed");
            }

            // Sleep for 1 second
            sleep(1);
        }

        $endTime = now();
        $actualDuration = $endTime->diffInSeconds($startTime);

        Log::info("Long running job completed in {$actualDuration} seconds");
    }
}
