<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FixApiRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:api-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix API routes and try direct database updates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Attempting to fix API routing issues...');
        
        // Step 1: Clear all caches
        $this->info('Clearing route cache...');
        Artisan::call('route:clear');
        
        $this->info('Clearing configuration cache...');
        Artisan::call('config:clear');
        
        $this->info('Clearing application cache...');
        Artisan::call('cache:clear');
        
        // Step 2: List all routes
        $this->info('Listing all registered routes:');
        $routes = Artisan::call('route:list');
        $this->line(Artisan::output());
        
        // Step 3: Try direct database update
        $this->info('Attempting direct database update as a workaround...');
        Artisan::call('test:direct-status-update');
        $this->line(Artisan::output());
        
        $this->info('Creating a test route directly in web.php...');
        
        // Create a simple route in web.php to test
        $webRoutesPath = base_path('routes/web.php');
        $webRoutes = file_get_contents($webRoutesPath);
        
        if (!str_contains($webRoutes, "Route::get('/api-test'")) {
            $testRoute = "
Route::get('/api-test', function () {
    return response()->json([
        'message' => 'API test route is working',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

Route::post('/status-metrics-alt', function (\\Illuminate\\Http\\Request \$request) {
    \\App\\Models\\StatusMetric::create([
        'source' => \$request->source ?? 'test',
        'key' => \$request->key ?? 'test',
        'value' => \$request->value ?? 'test',
        'status' => \$request->status ?? 'ok',
        'metadata' => \$request->metadata,
        'expires_at' => now()->addDays(60),
    ]);
    
    return response()->json([
        'message' => 'Status metric created successfully',
        'timestamp' => now()->toDateTimeString(),
    ]);
});
";
            file_put_contents($webRoutesPath, $webRoutes . $testRoute);
            $this->info('Added test routes to web.php');
        } else {
            $this->info('Test routes already exist in web.php');
        }
        
        $this->info('All done! Now try these URLs:');
        $this->line('1. http://laravel-pulse-sandbox.test/api-test');
        $this->line('2. Use the command: php artisan test:direct-status-update');
        $this->line('3. POST to http://laravel-pulse-sandbox.test/status-metrics-alt');
        
        return self::SUCCESS;
    }
}