<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('templates')->insert([
            [
                'name' => 'Business Template',
                'slug' => Str::random(10),
                'image' => '1718483175.jpg',
                'description' => 'A professional business template.',
                'price' => 49.99,
                'live_link' => 'https://innoshop.chibuikeinnocent.tech',
                'downloads' => 120,
                'category' => 'Business',
                'technologies' => 'React,Tailwind CSS,Laravel',
               
                'license' => 'Standard',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Portfolio Template',
                'slug' => Str::random(10),
                'image' => '1718486207.jpg',
                'description' => 'A sleek and modern portfolio template.',
                'price' => 29.99,
                'live_link' => 'https://innoblog.com.ng',
                'downloads' => 85,
                'category' => 'Portfolio',
                'technologies' => 'React,Tailwind CSS',
                
                'license' => 'Standard',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
