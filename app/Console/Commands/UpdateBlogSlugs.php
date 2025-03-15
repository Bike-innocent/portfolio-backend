<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;

class UpdateBlogSlugs extends Command
{
    protected $signature = 'update:blog-slugs';
    protected $description = 'Update all existing blog slugs to follow the new format';

    public function handle()
    {
        $this->info('Updating blog slugs...');

        Blog::all()->each(function ($blog) {
            $blog->slug = Blog::generateSlug($blog->title);
            $blog->save();
        });

        $this->info('Blog slugs updated successfully!');
    }
}