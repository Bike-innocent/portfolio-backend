<?php


namespace App\Http\Controllers\posts;
use App\Http\Controllers\Controller;


use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController2 extends Controller
{
    public function show($slug)
    {
        try {
            $category = Category::where('slug', $slug)->with('subcategories', 'posts')->firstOrFail();
            return response()->json($category, 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching category: ' . $e->getMessage());
            return response()->json(['error' => 'Category not found'], 404);
        }
    }
    

    public function getPostsBySubCategory($categorySlug, $subcategorySlug)
    {
        $subcategory = SubCategory::where('slug', $subcategorySlug)->whereHas('category', function ($query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        })->with('posts')->firstOrFail();

        return response()->json([
            'subcategoryName' => $subcategory->name,
            'posts' => $subcategory->posts,
        ]);
    }
}