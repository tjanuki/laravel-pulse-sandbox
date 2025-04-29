<?php

namespace App\Console\Commands;

use App\Models\StatusMetric;
use Illuminate\Console\Command;

class PurgeOldStatusMetrics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:purge-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge status metrics older than their expiry date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = StatusMetric::where('expires_at', '<', now())->delete();
        
        $this->info("Purged {$count} expired status metrics");
        
        return self::SUCCESS;
    }
}