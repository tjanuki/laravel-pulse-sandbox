<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BlogTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users if needed
        if (User::count() === 0) {
            User::factory(5)->create();
        }
        
        $userIds = User::pluck('id')->toArray();
        
        // Create some blogs from the past
        Blog::factory()
            ->count(30)
            ->sequence(fn ($sequence) => [
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'user_id' => $userIds[array_rand($userIds)]
            ])
            ->create();
            
        // Create some blogs today
        Blog::factory()
            ->count(10)
            ->sequence(fn ($sequence) => [
                'created_at' => Carbon::today()->addHours(rand(0, 12)),
                'user_id' => $userIds[array_rand($userIds)]
            ])
            ->create();
            
        // Create some blogs in the last hour
        $createInLastHour = rand(0, 5); // Randomly create 0-5 blogs in the last hour
        
        if ($createInLastHour > 0) {
            Blog::factory()
                ->count($createInLastHour)
                ->sequence(fn ($sequence) => [
                    'created_at' => Carbon::now()->subMinutes(rand(1, 59)),
                    'user_id' => $userIds[array_rand($userIds)]
                ])
                ->create();
                
            $this->command->info("{$createInLastHour} blogs created in the last hour");
        } else {
            $this->command->info("No blogs created in the last hour (to test alert state)");
        }
        
        $this->command->info("Total of " . (30 + 10 + $createInLastHour) . " test blogs created");
    }
}