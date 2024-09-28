<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;

class SitemapController extends Controller
{
    public function generateSitemap()
    {
        // Create a new sitemap instance
        $sitemap = Sitemap::create();

        // Add React Frontend URLs
        $sitemap->add(Url::create('https://innoblog.com.ng/'));
                // Add other static pages as needed
                // ->add(Url::create('https://innoblog.com.ng/about'))
                // ->add(Url::create('https://innoblog.com.ng/contact'));

        // Fetch all your blog posts and add them to the sitemap
        $posts = Post::all(); // Fetch posts from the database
        foreach ($posts as $post) {
            $sitemap->add(Url::create("https://innoblog.com.ng/posts/{$post->slug}")
                             ->setLastModificationDate($post->updated_at)
                             ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                             ->setPriority(0.8));
        }

        // Write the sitemap to the public directory
        $sitemap->writeToFile(public_path('sitemap.xml'));

        return response()->json(['message' => 'Sitemap generated successfully']);
    }
}

