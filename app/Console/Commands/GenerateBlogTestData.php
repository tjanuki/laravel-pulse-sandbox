<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class GenerateBlogTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blogs:generate {count=10 : Number of blog posts to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test blog data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int) $this->argument('count');
        $faker = Faker::create();
        
        // Make sure we have at least one user
        $user = User::first();
        if (!$user) {
            $this->info('Creating default user...');
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
        
        $this->info("Generating {$count} blog posts...");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        for ($i = 0; $i < $count; $i++) {
            $title = $faker->sentence();
            $timeAdjustment = rand(0, 60 * 24 * 7); // Random time within the last week
            
            Blog::create([
                'title' => $title,
                'content' => $faker->paragraphs(rand(3, 7), true),
                'slug' => Str::slug($title) . '-' . Str::random(5),
                'user_id' => $user->id,
                'published' => $faker->boolean(80),
                'published_at' => $faker->boolean(80) ? now()->subMinutes($timeAdjustment) : null,
                'created_at' => now()->subMinutes($timeAdjustment),
                'updated_at' => now()->subMinutes(rand(0, $timeAdjustment)),
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully generated {$count} blog posts!");
        $this->info("Run 'blogs:report-metrics' to update the status metrics.");
        
        return self::SUCCESS;
    }
}