<?php

namespace App\Http\Controllers\posts;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{

// public function index(Request $request)
// {
//     $limit = $request->query('limit', 12); // Default limit is 12

//     $user = auth()->user(); // Get the authenticated user, if available

//     // Fetch posts with user data and randomly order them
//     $posts = Post::with('user')->inRandomOrder()->paginate($limit);

//     // Transform posts to include full URL for images, user avatars, and is_saved status
//     $posts->getCollection()->transform(function ($post) use ($user) {
//         // Set the image URL
//         $post->image = url('post-images/' . $post->image);

//         // Set user avatar URL or placeholder color
//         if ($post->user && $post->user->avatar) {
//             $post->user->avatar_url = url('avatars/' . $post->user->avatar);
//         } else {
//             $post->user->avatar_url = null;
//             $post->user->placeholder_color = $post->user->placeholder_color;
//         }

//         // Check if the authenticated user has saved this post
//         $post->is_saved = $user ? $user->savedPosts()->where('post_id', $post->id)->exists() : false;

//         return $post;
//     });

//     return response()->json($posts);
// }


// public function index(Request $request)
// {
//     $limit = $request->query('limit', 12); // Default limit is 12
//     $page = $request->query('page', 1); // Get the current page from the query
//     $cacheKey = 'random_posts_' . auth()->id() . '_page_' . $page; // Unique cache key per user and page
//     $cacheDuration = 1 * 60; // Cache for 3 minutes

//     $user = auth()->user(); // Get the authenticated user, if available

//     // Check if cached posts for the current page exist, otherwise fetch and cache them
//     $posts = Cache::remember($cacheKey, $cacheDuration, function () use ($limit, $page) {
//         // Fetch only published posts where status = 1 and randomize the order
//         return Post::with('user')
//             ->where('status', 1)
//             ->inRandomOrder()
//             ->paginate($limit, ['*'], 'page', $page);
//     });

//     // Transform posts to include full URL for images, user avatars, and is_saved status
//     $posts->getCollection()->transform(function ($post) use ($user) {
//         // Set the image URL
//         $post->image = url('post-images/' . $post->image);

//         // Set user avatar URL or placeholder color
//         if ($post->user && $post->user->avatar) {
//             $post->user->avatar_url = url('avatars/' . $post->user->avatar);
//         } else {
//             $post->user->avatar_url = null;
//             $post->user->placeholder_color = $post->user->placeholder_color;
//         }

//         // Check if the authenticated user has saved this post
//         $post->is_saved = $user ? $user->savedPosts()->where('post_id', $post->id)->exists() : false;

//         return $post;
//     });

//     return response()->json($posts);
// }



public function index(Request $request)
{
    $limit = $request->query('limit', 12); // Default limit is 12
    $page = $request->query('page', 1); // Current page number
    $user = auth()->user(); // Authenticated user
    $cacheDuration = 4 * 60; // Cache duration in seconds (3 minutes)

    // Unique cache key based on user ID and total posts
    $cacheKey = 'random_post_ids_' . auth()->id();

    // Check if the random order of post IDs is cached, else generate it
    $postIds = Cache::remember($cacheKey, $cacheDuration, function () {
        // Fetch all published posts where status = 1 and shuffle their IDs
        return Post::where('status', 1)->pluck('id')->shuffle()->toArray();
    });

    // Paginate the cached post IDs
    $slicedPostIds = array_slice($postIds, ($page - 1) * $limit, $limit);

    // Fetch the posts corresponding to the paginated IDs
    $posts = Post::with('user')
        ->whereIn('id', $slicedPostIds)
        ->orderByRaw('FIELD(id, ' . implode(',', $slicedPostIds) . ')') // Preserve the random order
        ->get();

    // Transform posts to include full URL for images, user avatars, and is_saved status
    $posts->transform(function ($post) use ($user) {
        // Set the image URL
        $post->image = url('post-images/' . $post->image);

        // Set user avatar URL or placeholder color
        if ($post->user && $post->user->avatar) {
            $post->user->avatar_url = url('avatars/' . $post->user->avatar);
        } else {
            $post->user->avatar_url = null;
            $post->user->placeholder_color = $post->user->placeholder_color;
        }

        // Check if the authenticated user has saved this post
        $post->is_saved = $user ? $user->savedPosts()->where('post_id', $post->id)->exists() : false;

        return $post;
    });

    // Total number of pages
    $totalPages = ceil(count($postIds) / $limit);

    // Return the posts along with pagination data
    return response()->json([
        'posts' => $posts,
        'current_page' => (int) $page,
        'total_pages' => $totalPages,
        'total_posts' => count($postIds),
    ]);
}





    // Method to like/unlike a post
    public function like($slug)
    {
        $user = auth()->user();

        // Find the post by slug
        $post = Post::where('slug', $slug)->firstOrFail();

        // Check if the user has already liked the post
        $like = Like::where('post_id', $post->id)->where('user_id', $user->id)->first();

        if ($like) {
            // If like exists, delete it (unlike)
            $like->delete();
            $isLikedByUser = false;
        } else {
            // If like doesn't exist, create a new one
            Like::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);
            $isLikedByUser = true;
        }

        return response()->json([
            'likes_count' => $post->likes()->count(),
            'is_liked_by_user' => $isLikedByUser,
        ]);
    }

    // Method to save/unsave a post
    public function toggleSave($slug)
    {
        $user = auth()->user();
        $post = Post::where('slug', $slug)->firstOrFail();

        if ($user->savedPosts()->where('post_id', $post->id)->exists()) {
            $user->savedPosts()->detach($post->id); // Unsave
            $isSaved = false;
        } else {
            $user->savedPosts()->attach($post->id); // Save
            $isSaved = true;
        }

        return response()->json([
            'is_saved' => $isSaved,
        ]);
    }
    public function isPostSaved($slug)
{
    $user = auth()->user();
    $post = Post::where('slug', $slug)->firstOrFail();

    $isSaved = $user->savedPosts()->where('post_id', $post->id)->exists();

    return response()->json([
        'is_saved' => $isSaved,
    ]);
}


public function removeSave($slug)
{
    try {
        $user = auth()->user();
        $post = Post::where('slug', $slug)->firstOrFail();

        if ($user->savedPosts()->where('post_id', $post->id)->exists()) {
            $user->savedPosts()->detach($post->id); // Remove from saved posts
            $isRemoved = true;
        } else {
            $isRemoved = false;
        }

        return response()->json([
            'is_removed' => $isRemoved,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}




    // Method to fetch saved posts
    // public function getSavedPosts(Request $request)
    // {
    //     $user = auth()->user();

    //     // Fetch the saved posts with pagination
    //     $savedPosts = $user->savedPosts()->with('user')->paginate($request->get('limit', 12));

    //     // Transform the saved posts to include the full URL for images and user avatars
    //     $savedPosts->getCollection()->transform(function ($post) {
    //         $post->image = url('post-images/' . $post->image);
    //         if ($post->user && $post->user->avatar) {
    //             $post->user->avatar_url = url('avatars/' . $post->user->avatar);
    //         } else {
    //             $post->user->avatar_url = null;
    //             $post->user->placeholder_color = $post->user->placeholder_color;
    //         }
    //         return $post;
    //     });


    //     return response()->json($savedPosts);
    // }
    public function getSavedPosts(Request $request)
{
    $user = auth()->user();

    // Fetch the saved posts with pagination and order them by the most recently created (descending order)
    $savedPosts = $user->savedPosts()
        ->with('user')
        ->orderBy('created_at', 'desc') // Order by 'created_at' in descending order
        ->paginate($request->get('limit', 12));

    // Transform the saved posts to include the full URL for images and user avatars
    $savedPosts->getCollection()->transform(function ($post) {
        $post->image = url('post-images/' . $post->image);
        if ($post->user && $post->user->avatar) {
            $post->user->avatar_url = url('avatars/' . $post->user->avatar);
        } else {
            $post->user->avatar_url = null;
            $post->user->placeholder_color = $post->user->placeholder_color;
        }
        return $post;
    });

    return response()->json($savedPosts);
}




public function show2($slug)
{
    // Fetch the post by slug
    $post = Post::where('slug', $slug)->firstOrFail();

    // Prepare the full URL for the image
    $imageUrl = url('post-images/' . $post->image); // Assuming images are stored in 'public/post-images/'

    // Pass post data to the view
    return view('post.show', [
        'title' => $post->title,
        'description' =>  $post->content,
        'image' => $imageUrl, // Full URL for the image
        'slug' => $slug
    ]);
}

// In PostController.php

public function showOgTags($slug)
{
    $post = Post::where('slug', $slug)->firstOrFail();

    return response()->json([
        'title' => $post->title,
        'description' => $post->excerpt,
        'image' => url('post-images/' . $post->image),
        'url' => 'https://innoblog.com.ng/posts/' . $slug,
    ]);
}



}
