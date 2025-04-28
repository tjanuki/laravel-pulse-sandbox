<?php

namespace App\Recorders;

use App\Models\Blog;
use Laravel\Pulse\Facades\Pulse;

class HourlyBlogsRecorder
{
    /**
     * Record the hourly blog metrics.
     */
    public function record(): void
    {
        // This is just a stub - the actual recording is done by the command
    }

    /**
     * Record blog count data
     */
    public function recordBlogCount(int $count): void
    {
        Pulse::set('hourly_blogs', 'count', (string) $count);
    }
}
