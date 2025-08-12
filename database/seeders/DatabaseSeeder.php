<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use App\Models\Video;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * nested seeding
         */
        User::factory()
        ->has(
            Video::factory()
            ->has(
                Comment::factory()
                ->state(fn(array $attributes, Video $video) => ['user_id' => $video->user_id])
                ->count(10), 
                'comments'
            )
            ->count(5),
            'videos'
        )
        ->count(10)->create();

        // Video::factory(5)->create();
        // Comment::factory(240)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
