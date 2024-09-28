<?php

namespace App\Http\Controllers\posts;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Models\Post;

class SinglePostController extends Controller
{


    // public function show($slug)
    // {
    //     // Fetch the post with category, user, and likes relationships
    //     $post = Post::with(['category', 'user', 'likes'])->where('slug', $slug)->firstOrFail();

    //     // Set the image URL
    //     $post->image = url('post-images/' . $post->image);

    //     // Set the avatar URL if it exists
    //     if ($post->user->avatar) {
    //         $post->user->avatar = url('avatars/' . $post->user->avatar);
    //     }

    //     // Add the likes count to the post
    //     $post->likes_count = $post->likes()->count();

    //     // Check if the user is authenticated
    //     if (auth()->check()) {
    //         $user = auth()->user();
    //         // If authenticated, check if the post is liked by the user
    //         $post->is_liked_by_user = $post->likes()->where('user_id', $user->id)->exists();
    //     } else {
    //         // If not authenticated, set is_liked_by_user to false
    //         $post->is_liked_by_user = false;
    //     }

    //     // Return the post as JSON response
    //     return response()->json($post);
    // }


    public function show($slug)
{
    // Fetch the post with category, user, and likes relationships
    $post = Post::with(['category', 'user', 'likes'])->where('slug', $slug)->firstOrFail();

    // Set the image URL
    $post->image = url('post-images/' . $post->image);

    // Set the avatar URL if it exists
    if ($post->user->avatar) {
        $post->user->avatar = url('avatars/' . $post->user->avatar);
    }

    // Add the likes count to the post
    $post->likes_count = $post->likes()->count();

    // Check if the user is authenticated
    if (auth()->check()) {
        $user = auth()->user();

        // Check if the post is liked by the user
        $post->is_liked_by_user = $post->likes()->where('user_id', $user->id)->exists();

        // Check if the post is saved by the user
        $post->is_saved = $user->savedPosts()->where('post_id', $post->id)->exists();
    } else {
        // If not authenticated, set both is_liked_by_user and is_saved_by_user to false
        $post->is_liked_by_user = false;
        $post->is_saved = false;
    }

    // Return the post as JSON response
    return response()->json($post);
}




    // public function related($slug)
    // {
    //     $post = Post::where('slug', $slug)->firstOrFail();
    //     $relatedPosts = Post::with('user', 'category')
    //         ->where('category_id', $post->category_id)
    //         ->where('slug', '!=', $slug)
    //         ->take(5)
    //         ->latest()
    //         ->get();

    //     foreach ($relatedPosts as $relatedPost) {
    //         $relatedPost->image = url('post-images/' . $relatedPost->image);
    //     }

    //     return response()->json(['relatedPosts' => $relatedPosts]);
    // }

//     public function related($slug)
// {
//     $post = Post::where('slug', $slug)->firstOrFail();

//     // Fetch first 20 posts in random order from the same category
//     $relatedPosts = Post::with('user', 'category')
//         ->where('category_id', $post->category_id)
//         ->where('slug', '!=', $slug)
//         ->inRandomOrder() // Randomize the order
//         ->limit(20) // Fetch the first 20 posts
//         ->take(5) // Limit to 5 posts
//         ->get();

//     // Update image URLs
//     foreach ($relatedPosts as $relatedPost) {
//         $relatedPost->image = url('post-images/' . $relatedPost->image);
//     }

//     return response()->json(['relatedPosts' => $relatedPosts]);
// }



public function related($slug)
{
    $post = Post::where('slug', $slug)->firstOrFail();

    // Cache the related posts for 2 minutes based on the category
    $cacheKey = 'related_posts_category_' . $post->category_id;

    $relatedPosts = Cache::remember($cacheKey, 60, function () use ($post, $slug) {
        // Fetch first 20 posts in random order from the same category
        return Post::with('user', 'category')
            ->where('category_id', $post->category_id)
            ->where('slug', '!=', $slug)
            ->where('status', 1) // Fetch only published posts
            ->inRandomOrder() // Randomize the order
            ->limit(20) // Fetch the first 20 posts
            ->take(5) // Limit to 5 posts
            ->get()
            ->each(function ($relatedPost) {
                $relatedPost->image = url('post-images/' . $relatedPost->image);
            });
    });

    return response()->json(['relatedPosts' => $relatedPosts]);
}



}
