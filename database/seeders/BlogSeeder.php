<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Blog;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            '1718486207.jpg',
            '1718488178.jpg',
            '1718486207.jpg',
            '1718483175.jpg',
            '1718491950.jpg',
            '1718566073.jpg',
            '1718577499.jpg',
            '1718566337.jpg',
        ];

        $blogs = [
            [
                'title' => 'First Blog Post',
                'description' => 'This is the first blog post description.',
                'image' => $images[array_rand($images)],
                'category' => 'Technology',
            ],
            [
                'title' => 'Second Blog Post',
                'description' => 'This is the second blog post description.',
                'image' => $images[array_rand($images)],
                'category' => 'Web Development',
            ],
            [
                'title' => 'Third Blog Post',
                'description' => 'This is the third blog post description.',
                'image' => $images[array_rand($images)],
                'category' => 'Backend Development',
            ],
            [
                'title' => 'Fourth Blog Post',
                'description' => 'This is the fourth blog post description.',
                'image' => $images[array_rand($images)],
                'category' => 'Frontend Development',
            ],
            [
                'title' => 'Fifth Blog Post',
                'description' => 'This is the fifth blog post description.',
                'image' => $images[array_rand($images)],
                'category' => 'Design',
            ],
            [
                'title' => 'Sixth Blog Post',
                'description' => 'This is the sixth blog post description.',
                'image' => $images[array_rand($images)],
                'category' => 'Business',
            ],
        ];

        // Insert into the blogs table
        foreach ($blogs as &$blog) {
            // Generate a slug for each blog entry
            $blog['slug'] = Blog::generateSlug();
        }

        DB::table('blogs')->insert($blogs);
    }
}
