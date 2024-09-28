<?php

// namespace App\Http\Controllers\Posts;

// use App\Http\Controllers\Controller;
// use App\Models\Post;


// class SportController extends Controller
// {


//     public function sportSection()
//     {
//         $posts = Post::with(['user', 'category'])
//                     ->whereHas('category', function ($query) {
//                         $query->where('name', 'Sport');
//                     })
//                     ->latest()
//                     ->take(10)
//                     ->get();

//         $posts->transform(function ($post) {
//             $post->image = url('post-images/' . $post->image);
//             if ($post->user && $post->user->avatar) {
//                 $post->user->avatar = url('avatars/' . $post->user->avatar);
//             }
//             return $post;
//         });

//         return response()->json($posts);
//     }

// }
