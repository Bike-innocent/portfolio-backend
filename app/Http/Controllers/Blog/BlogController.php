<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // Fetch all blogs
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->get();

        // Append the image path
        $blogs->transform(function ($blog) {
            if ($blog->image) {
                $blog->image = url('blog-images/' . $blog->image);
            }
            return $blog;
        });

        return response()->json($blogs, 200);
    }

    // Fetch a single blog by SLUG
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        if ($blog->image) {
            $blog->image = url('blog-images/' . $blog->image);
        }

        return response()->json($blog, 200);
    }











    // // Create a new blog
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'category' => 'nullable|string|max:255',
    //         'image' => 'image|mimes:jpg,jpeg,png|max:5048',
    //     ]);

    //     $imageName = null;
    //     if ($request->hasFile('image')) {
    //         $imageName = time() . '.' . $request->image->extension();
    //         $request->image->move(public_path('blog-images'), $imageName);
    //     }

    //     // Automatically generate a random 8-character slug
    //     $blog = new Blog();
    //     $blog->title = $request->title;
    //     $blog->slug = Blog::generateSlug($blog->title);  // Using the random slug generator from the model
    //     $blog->description = $request->description;
    //     $blog->category = $request->category;
    //     $blog->image = $imageName;
    //     $blog->save();

    //     return response()->json($blog, 201);
    // }



    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'category' => 'nullable|string|max:255',
        'image' => 'image|mimes:jpg,jpeg,png|max:5048',
    ]);

    $imageName = null;
    if ($request->hasFile('image')) {
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('blog-images'), $imageName);
    }

    // Generate slug from title
    $slug = Blog::generateSlug($request->title);

    $blog = new Blog();
    $blog->title = $request->title;
    $blog->slug = $slug; // Use the title-based slug
    $blog->description = $request->description;
    $blog->category = $request->category;
    $blog->image = $imageName;
    $blog->save();

    return response()->json($blog, 201);
}









    // Update an existing blog
    public function update(Request $request, $slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5048',
        ]);

        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->category = $request->category ?? $blog->category;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('blog-images'), $imageName);
            $blog->image = $imageName;
        }

        $blog->save();

        return response()->json(['message' => 'Blog updated successfully', 'blog' => $blog], 200);
    }

    // Delete a blog
    public function destroy($slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        $blog->delete();

        return response()->json(['message' => 'Blog deleted'], 200);
    }

    // Fetch related blogs
    public function getRelatedBlogs($slug)
    {
        // Find the current blog by slug
        $currentBlog = Blog::where('slug', $slug)->firstOrFail();

        // Fetch other blogs excluding the current one
        $relatedBlogs = Blog::where('id', '!=', $currentBlog->id) // Exclude the current blog
            ->get();

        // Check if no related blogs are found, return an empty array
        if ($relatedBlogs->isEmpty()) {
            return response()->json([], 200); // Return empty array
        }

        // Loop through related blogs and append the correct image directory path
        foreach ($relatedBlogs as $blog) {
            if ($blog->image) {
                $blog->image = url('blog-images/' . $blog->image);
            }
        }

        // Return the related blogs as JSON response
        return response()->json($relatedBlogs, 200);
    }

}





// ok i have a laravel slug logic like this for my blog  public static function generateSlug()
// <?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

// class Blog extends Model
// {
//     use HasFactory;

//     protected $guarded = [];

//     public static function boot()
//     {
//         parent::boot();

//         // Automatically generate a random slug when creating a new blog post
//         static::creating(function ($blog) {
//             if (empty($blog->slug)) {
//                 $blog->slug = self::generateSlug();
//             }
//         });
//     }

//     /**
//      * Generate a unique 8-character slug.
//      *
//      * @return string
//      */
//     public static function generateSlug()
//     {
//         $slug = Str::random(8);

//         // Ensure the slug is unique by checking the database
//         while (self::where('slug', $slug)->exists()) {
//             $slug = Str::random(8);
//         }

//         return $slug;
//     }
// }
// but i want the slug to be changed from random to the blog title but will be in slug format for url  here is table  Schema::create('blogs', function (Blueprint $table) {
//             $table->id();
//             $table->string('title');
//             $table->string('slug')->unique();  // Unique slug for SEO-friendly URLs
//             $table->text('description');
//             $table->string('image');  // This will store the image path
//             $table->string('category')->nullable();  // Category of the blog (nullable)
//             $table->timestamps();  // Handles created_at and updated_at
//         });  and here is the store method public function store(Request $request)
//     {
//         $request->validate([
//             'title' => 'required|string|max:255',
//             'description' => 'required|string',
//             'category' => 'nullable|string|max:255',
//             'image' => 'image|mimes:jpg,jpeg,png|max:5048',
//         ]);

//         $imageName = null;
//         if ($request->hasFile('image')) {
//             $imageName = time() . '.' . $request->image->extension();
//             $request->image->move(public_path('blog-images'), $imageName);
//         }

//         // Automatically generate a random 8-character slug
//         $blog = new Blog();
//         $blog->title = $request->title;
//         $blog->slug = Blog::generateSlug();  // Using the random slug generator from the model
//         $blog->description = $request->description;
//         $blog->category = $request->category;
//         $blog->image = $imageName;
//         $blog->save();

//         return response()->json($blog, 201);
//     }  maybe you will aplly the logic directly