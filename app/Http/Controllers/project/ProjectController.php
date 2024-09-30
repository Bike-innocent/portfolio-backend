<?php

namespace App\Http\Controllers\project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Fetch all projects
    public function index()
    {
        // Retrieve projects ordered by the most recently created
        $projects = Project::orderBy('created_at', 'desc')->get();
    
        // Loop through each project and adjust the image path
        $projects->transform(function ($project) {
            if ($project->image) {
                // Append the correct image directory path
                $project->image = url('project-images/' . $project->image);
            }
            return $project;
        });
    
        // Return the sorted projects as a JSON response
        return response()->json($projects, 200);
    }
    

    // Fetch a single project by ID
    public function show($slug)
    {
        $project = Project::where('slug', $slug)->first();
    
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        if ($project->image) {
            // Append the correct image directory path
            $project->image = url('project-images/' . $project->image);
        }

       
    
        return response()->json($project, 200);
    }
    
    // Create a new project
    public function store(Request $request)
    {
        // Validation for all the fields
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'client' => 'nullable|string|max:255',
            'tools' => 'nullable|string|max:255',
            'start_date' => 'nullable|string|max:255',
            'end_date' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'image' => 'image|mimes:jpg,jpeg,png|max:2048', // Optional image validation
        ]);

        // Create a new project
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('project-images'), $imageName);

        $project = new Project();
        $project->name = $request->name;
        $project->description = $request->description;
        $project->client = $request->client;
        $project->tools = $request->tools;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->category = $request->category;
        $project->url = $request->url;
        $project->image = $imageName;

        // Handle image upload


        $project->save();

        return response()->json($project, 201);
    }

    // Update an existing project
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Validation for all the fields
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'client' => 'nullable|string|max:255',
            'tools' => 'nullable|string|max:255',
            'start_date' => 'nullable|string|max:255',
            'end_date' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Optional image validation
        ]);

        // Update project fields
        $project->name = $request->name;
        $project->description = $request->description;
        $project->client = $request->client;
        $project->tools = $request->tools;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->category = $request->category;
        $project->url = $request->url;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('project-images', 'public');
            $project->image = $imagePath;
        }

        $project->save();

        return response()->json($project, 200);
    }

    // Delete a project
    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted'], 200);
    }
}
