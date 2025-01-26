<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of all templates.
     */
    public function index()
    {
        $templates = Template::all();

        $templates->transform(function ($templates) {
            if ($templates->image) {
                $templates->image = url('template-images/' . $templates->image);
            }
            return $templates;
        });

        return response()->json($templates, 200);
    }

    // Create a new template
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'live_link' => 'nullable|url',
            'category' => 'nullable|string|max:255',
            'image' => 'image|mimes:jpg,jpeg,png|max:5048',
            'technologies' => 'nullable|string',
            
            'license' => 'nullable|string|max:255',
        ]);

        // Handle image upload
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('template-images'), $imageName);
        }

        // Create and save the template
        $template = new Template();
        $template->name = $request->name;
        $template->slug = Template::generateSlug(); // Use slug generator from the model
        $template->description = $request->description;
        $template->price = $request->price;
        $template->live_link = $request->live_link;
        $template->category = $request->category;
        $template->image = $imageName;
        $template->technologies = $request->technologies;
        $template->license = $request->license ?? 'Standard'; // Default value
        $template->save();

        return response()->json($template, 201);
    }


    /**
     * Display a single template by its slug.
     */
    public function show($slug)
    {
        $template = Template::where('slug', $slug)->first();

        

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found',
            ], 404);
        }

        if ($template->image) {
            $template->image = url('template-images/' . $template->image);
        }


        return response()->json($template, 200);
    }







    public function update(Request $request, $slug)
    {
        // Find the template by slug
        $template = Template::where('slug', $slug)->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found',
            ], 404);
        }

        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'live_link' => 'nullable|url',
            'category' => 'nullable|string|max:255',
            'image' => 'image|mimes:jpg,jpeg,png|max:5048',
            'technologies' => 'nullable|string',
            'license' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($template->image && file_exists(public_path('template-images/' . $template->image))) {
                unlink(public_path('template-images/' . $template->image));
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('template-images'), $imageName);
            $template->image = $imageName;
        }

        // Update template fields
        $template->name = $request->name;
        $template->description = $request->description;
        $template->price = $request->price;
        $template->live_link = $request->live_link;
        $template->category = $request->category;
        $template->technologies = $request->technologies;
        $template->license = $request->license ?? 'Standard'; // Default value
        $template->status = $request->status ?? false; // Default to false if not provided

        $template->save();

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully',
            'template' => $template,
        ], 200);
    }


    public function destroy($slug)
    {
        $template = Template::where('slug', $slug)->first();

        if (!$template) {
            return response()->json(['message' => 'template not found'], 404);
        }

        $template->delete();

        return response()->json(['message' => 'template deleted'], 200);
    }


}

