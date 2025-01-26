<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'rating' => 5,
                'review_text' => 'An excellent template for businesses!',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_id' => 2,
                'reviewer_name' => 'Jane Smith',
                'rating' => 4,
                'review_text' => 'Great design, but could use more documentation.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
