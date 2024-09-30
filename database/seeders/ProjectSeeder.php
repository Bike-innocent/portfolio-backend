<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
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

        $projects = [
            [
                'name' => 'Project One',
                'description' => 'This is the first project description.',
                'image' => $images[array_rand($images)],
                'client' => 'Client A',
                'tools' => 'Laravel, Vue.js',
                'start_date' => '2023-01-01',
                'end_date' => '2023-01-30',
                'category' => 'Web Development',
                'url' => 'https://project-one.com',
                'slug' => Str::random(8),  // Generate a random slug
            ],
            [
                'name' => 'Project Two',
                'description' => 'This is the second project description.',
                'image' => $images[array_rand($images)],
                'client' => 'Client B',
                'tools' => 'React, Tailwind CSS',
                'start_date' => '2023-02-01',
                'end_date' => '2023-02-28',
                'category' => 'Frontend Development',
                'url' => 'https://project-two.com',
                'slug' => Str::random(8),  // Generate a random slug
            ],
            [
                'name' => 'Project Three',
                'description' => 'This is the third project description.',
                'image' => $images[array_rand($images)],
                'client' => 'Client C',
                'tools' => 'PHP, Bootstrap',
                'start_date' => '2023-03-01',
                'end_date' => '2023-03-30',
                'category' => 'Backend Development',
                'url' => 'https://project-three.com',
                'slug' => Str::random(8),  // Generate a random slug
            ],
        ];

        // Insert into the projects table
        DB::table('projects')->insert($projects);
    }
}
