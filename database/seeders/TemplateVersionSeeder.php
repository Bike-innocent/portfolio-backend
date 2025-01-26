<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('template_versions')->insert([
            [
                'template_id' => 1,
                'version' => '1.0.0',
                'release_date' => '2024-01-01',
                'last_updated_date' => '2024-01-10',
                'updates' => json_encode([
                    'Initial release with basic features.',
                    'Added responsive design for mobile devices.',
                    'Fixed minor bugs in the contact form.',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'template_id' => 2,
                'version' => '2.1.0',
                'release_date' => '2024-01-05',
                'last_updated_date' => '2024-01-15',
                'updates' => json_encode([
                    'Updated the user authentication system.',
                    'Integrated Tailwind CSS for enhanced styling.',
                    'Optimized image loading for faster performance.',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
