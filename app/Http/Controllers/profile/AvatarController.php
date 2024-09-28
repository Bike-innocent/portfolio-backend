<?php

namespace App\Http\Controllers\profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUpdateRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $user = Auth::user();
    //     $avatarPath = $user->avatar ? url('avatars/' . $user->avatar) : null;

    //     // Assuming you have a placeholder color attribute in your user model
    //     $placeholderColor = $user->placeholder_color; // Adjust this if your attribute name is different

    //     return response()->json([
    //         'avatar' => $avatarPath,
    //         'name' => $user->name,
    //         'placeholder_color' => $placeholderColor // Add this to the response
    //     ], 200);
    // }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AvatarUpdateRequest $request)
    {
        $user = Auth::user();

        if ($user->avatar) {
            $oldAvatarPath = public_path('avatars/' . $user->avatar);
            if (File::exists($oldAvatarPath)) {
                File::delete($oldAvatarPath);
            }
        }

        $imageName = time() . '.' . $request->avatar->extension();
        $request->avatar->move(public_path('avatars'), $imageName);

        $user->avatar = $imageName;
        $user->save();

        return response()->json(['avatar' => url('avatars/' . $imageName)], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        $user = Auth::user();
        if ($user->avatar) {
            $avatarPath = public_path('avatars/' . $user->avatar);
            if (File::exists($avatarPath)) {
                File::delete($avatarPath);
            }
            $user->avatar = null;
            $user->save();
        }

        return response()->json(['message' => 'Avatar deleted'], 200);
    }
}
