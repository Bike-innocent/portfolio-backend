<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $user->load('roles', 'permissions');

        // Construct the URL for the avatar if it exists
        if ($user->avatar) {
            $user->avatar = url('avatars/' . $user->avatar);
        }

        return response()->json($user);
    }


    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $validatedData = $request->validated();

        // Update user profile fields
        $user->name = $validatedData['name'];
       
        if ($request->has('username')) {
            // Ensure the "@" is prepended when saving to the database
            $username = '@' . ltrim($validatedData['username'], '@');
            $user->username = $username;
        }

        try {
            $user->save();
            return response()->json(['message' => 'Profile updated successfully.'], 200);
        } catch (QueryException $e) {
            // Check for a duplicate username
            if ($e->errorInfo[1] === 1062) {
                return response()->json(['errors' => ['username' => 'Username is not avaliable.']], 422);
            }

            return response()->json(['message' => 'An error occurred while updating the profile.'], 500);
        }
    }



    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => ['password' => 'The provided password does not match our records.']], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Account deleted successfully.'], 200);
    }

    public function show($username)
    {
        \Log::info('Looking for user with username: ' . $username);
        $user = User::where('username', $username)->first();
        if (!$user) {
            \Log::error('User not found with username: ' . $username);
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->avatar) {
            $user->avatar = url('avatars/' . $user->avatar);
        }

        return response()->json($user);
    }

    // public function getPostsByUsername($username)
    // {
    //     $user = User::where('username', $username)->firstOrFail();
    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $posts = Post::where('user_id', $user->id)->paginate(12);
    //     foreach ($posts as $post) {
    //         $post->image = url('post-images/' . $post->image);
    //     }

    //     return response()->json($posts);
    // }

    public function getPostsByUsername($username)
{
    $user = User::where('username', $username)->firstOrFail();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Fetch user's posts, order by most recent, and paginate
    $posts = Post::where('user_id', $user->id)
                ->orderBy('created_at', 'desc') // Order by most recent
                ->paginate(12);

    // Update image URLs
    foreach ($posts as $post) {
        $post->image = url('post-images/' . $post->image);
    }

    // Return the posts as a JSON response
    return response()->json($posts);
}

}
