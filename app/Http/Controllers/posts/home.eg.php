
public function sportSection()
    {
        // Fetch posts with 'Sport' category
        $posts = Post::with(['user', 'category'])
            ->whereHas('category', function ($query) {
                $query->where('slug', 'travel');
            })
            ->latest()
            ->take(10)
            ->get();

        // // Debugging
        // if ($posts->isEmpty()) {
        //     return response()->json(['message' => 'No posts found in the Sport category'], 404);
        // }

        $posts->transform(function ($post) {
            $post->image = url('post-images/' . $post->image);
            if ($post->user && $post->user->avatar) {
                $post->user->avatar = url('avatars/' . $post->user->avatar);
            }
            return $post;
        });

        return response()->json($posts);
    }


    public function businessSection()
    {
        $posts = Post::with(['user', 'category'])
            ->whereHas('category', function ($query) {
                $query->where('slug', 'earth');
            })
            ->latest()
            ->take(10)
            ->get();


        $posts->transform(function ($post) {
            $post->image = url('post-images/' . $post->image);
            if ($post->user && $post->user->avatar) {
                $post->user->avatar = url('avatars/' . $post->user->avatar);
            }
            return $post;
        });

        return response()->json($posts);
    }

    public function technologySection()
    {
        // Fetch posts with 'Sport' category
        $posts = Post::with(['user', 'category'])
            ->whereHas('category', function ($query) {
                $query->where('slug', 'news');
            })
            ->latest()
            ->take(10)
            ->get();

        // // Debugging
        // if ($posts->isEmpty()) {
        //     return response()->json(['message' => 'No posts found in the Sport category'], 404);
        // }

        $posts->transform(function ($post) {
            $post->image = url('post-images/' . $post->image);
            if ($post->user && $post->user->avatar) {
                $post->user->avatar = url('avatars/' . $post->user->avatar);
            }
            return $post;
        });

        return response()->json($posts);
    }

