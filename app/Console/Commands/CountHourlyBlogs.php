<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Recorders\HourlyBlogsRecorder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Facades\Pulse;

class CountHourlyBlogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blogs:count-hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count blogs created in the last hour and record to Pulse';

    /**
     * Execute the console command.
     */
    public function handle(HourlyBlogsRecorder $recorder): int
    {
        // Get count of blogs created in the last hour
        $count = Blog::where('created_at', '>=', now()->subHour())->count();

        // Record the count to Pulse using the recorder
        $recorder->recordBlogCount($count);

        $this->info("Hourly blog count recorded: {$count}");

        return self::SUCCESS;
    }
}
