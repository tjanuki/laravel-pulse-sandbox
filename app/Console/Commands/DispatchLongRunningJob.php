<?php

namespace App\Console\Commands;

use App\Jobs\LongRunningJob;
use Illuminate\Console\Command;

class DispatchLongRunningJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-long-job {--time=30 : The job duration in seconds}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch a long running job for Laravel Pulse testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $time = (int) $this->option('time');

        $this->info("Dispatching a job that will run for {$time} seconds...");

        // Dispatch the job
        LongRunningJob::dispatch($time);

        $this->info("Job dispatched successfully! Check Laravel Pulse to monitor the job.");
    }
}
