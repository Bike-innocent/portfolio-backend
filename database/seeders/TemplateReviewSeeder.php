<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TemplateReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('template_reviews')->insert([
            [
                'template_id' => 1,
                'reviewer_name' => 'John Doe',
                'reviewer_email' => 'johndoe@example.com',
                'rating' => 5,
                'review_text' => 'An excellent template for businesses!',
                'token' => Str::random(40), // Generate a random token
                'status' => 'approved', // Set the initial status
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_id' => 2,
                'reviewer_name' => 'Jane Smith',
                'reviewer_email' => 'janesmith@example.com',
                'rating' => 4,
                'review_text' => 'Great design, but could use more documentation.',
                'token' => Str::random(40), // Generate a random token
                'status' => 'pending', // Set the initial status
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
