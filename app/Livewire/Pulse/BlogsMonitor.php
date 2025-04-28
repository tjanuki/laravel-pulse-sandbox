<?php

namespace App\Livewire\Pulse;

use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class BlogsMonitor extends Card
{
    /**
     * Render the component.
     */
    public function render()
    {
        // Get the count of blogs created in the last hour directly
        $hourlyBlogsCount = Blog::where('created_at', '>=', now()->subHour())->count();

        // In Pulse 1.4, we can't use Pulse::get() method
        // Instead, let's create a collection from the database directly
        $hourlyBlogsEntries = collect([
            [
                'value' => (string) $hourlyBlogsCount,
                'recorded_at' => now()->toDateTimeString(),
            ]
        ]);

        // Determine if we need to show an alert (if count is 0)
        $showAlert = $hourlyBlogsCount === 0;

        // Get some additional stats
        $totalBlogs = Blog::count();
        $todayBlogs = Blog::whereDate('created_at', today())->count();

        return view('livewire.pulse.blogs-monitor', [
            'hourlyBlogsCount' => $hourlyBlogsCount,
            'hourlyBlogsEntries' => $hourlyBlogsEntries,
            'showAlert' => $showAlert,
            'totalBlogs' => $totalBlogs,
            'todayBlogs' => $todayBlogs,
        ]);
    }
}
