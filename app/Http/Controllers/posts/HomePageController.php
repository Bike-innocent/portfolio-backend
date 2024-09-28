<?php



namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomePageController extends Controller
{
    // public function search(Request $request) {
    //     $query = $request->input('query');
    //     $limit = $request->input('limit', 12); // Number of results per page
    //     $page = $request->input('page', 1); // Current page

    //     // Correct common spelling mistakes
    //     $query = $this->correctSpelling($query);

    //     // Split the query into individual words
    //     $words = explode(' ', $query);

    //     // Perform the search for each word and combine results
    //     $results = Post::where(function ($q) use ($words) {
    //         foreach ($words as $word) {
    //             $q->orWhere('title', 'LIKE', "%{$word}%")
    //               ->orWhere('content', 'LIKE', "%{$word}%");
    //         }
    //     })
    //     ->orWhere(function ($q) use ($query) {
    //         // Additional keyword-based search
    //         $keywords = $this->extractKeywords($query);
    //         foreach ($keywords as $keyword) {
    //             $q->orWhere('title', 'LIKE', "%{$keyword}%")
    //               ->orWhere('content', 'LIKE', "%{$keyword}%");
    //         }
    //     })
    //     ->with('user') // Eager load the user relationship
    //     ->paginate($limit, ['*'], 'page', $page); // Use paginate for pagination

    //     // Add the full image URL and user avatar URL
    //     foreach ($results as $post) {
    //         $post->image = url('post-images/' . $post->image);
    //         $post->user->avatar_url = url('avatars/' . $post->user->avatar);
    //     }

    //     return response()->json($results);
    // }


    public function search(Request $request)
{
    $query = $request->input('query');
    $limit = $request->input('limit', 12); // Number of results per page
    $page = $request->input('page', 1); // Current page
    $user = auth()->user(); // Get the authenticated user

    // Correct common spelling mistakes
    $query = $this->correctSpelling($query);

    // Split the query into individual words
    $words = explode(' ', $query);

    // Perform the search for each word and combine results
    $results = Post::where(function ($q) use ($words) {
        foreach ($words as $word) {
            $q->orWhere('title', 'LIKE', "%{$word}%")
              ->orWhere('content', 'LIKE', "%{$word}%");
        }
    })
    ->orWhere(function ($q) use ($query) {
        // Additional keyword-based search
        $keywords = $this->extractKeywords($query);
        foreach ($keywords as $keyword) {
            $q->orWhere('title', 'LIKE', "%{$keyword}%")
              ->orWhere('content', 'LIKE', "%{$keyword}%");
        }
    })
    ->with('user') // Eager load the user relationship
    ->paginate($limit, ['*'], 'page', $page); // Use paginate for pagination

    // Add the full image URL, user avatar URL, and is_saved status for each post
    $results->getCollection()->transform(function ($post) use ($user) {
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

    return response()->json($results);
}


    // Basic spelling correction function
    protected function correctSpelling($query) {
        // Add your spelling corrections here
        $corrections = [
            'carr' => 'car',
            'driv' => 'drive',
            // Add more corrections as needed
        ];

        $words = explode(' ', $query);
        foreach ($words as &$word) {
            if (isset($corrections[$word])) {
                $word = $corrections[$word];
            }
        }

        return implode(' ', $words);
    }

    // Extract important keywords from the query
    protected function extractKeywords($query) {
        $keywords = [
            'dance', 'cook', 'drive', // Add more keywords
        ];

        $foundKeywords = [];
        foreach ($keywords as $keyword) {
            if (Str::contains($query, $keyword)) {
                $foundKeywords[] = $keyword;
            }
        }

        return $foundKeywords;
    }
}
