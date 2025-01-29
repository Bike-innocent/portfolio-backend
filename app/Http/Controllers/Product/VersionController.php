<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\TemplateVersion;
use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Validator;

class VersionController extends Controller
{
    public function store(Request $request, $templateId)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'version' => 'required|string|max:255',
            'release_date' => 'required|date',
            'last_updated_date' => 'nullable|date',
            'updates' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the template exists
        $template = Template::findOrFail($templateId);

        // Create a new version
        $version = TemplateVersion::create([
            'template_id' => $template->id,
            'version' => $request->version,
            'release_date' => $request->release_date,
            'last_updated_date' => $request->last_updated_date,
            'updates' => json_encode($request->updates), // Convert array to JSON
        ]);

        return response()->json([
            'message' => 'Version created successfully!',
            'version' => $version
        ], 201);
    }


    public function update(Request $request, $templateId, $versionId)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'version' => 'required|string|max:255',
            'release_date' => 'required|date',
            'last_updated_date' => 'nullable|date',
            'updates' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the template exists
        $template = Template::findOrFail($templateId);

        // Find the version to update
        $version = TemplateVersion::where('template_id', $template->id)
                                  ->where('id', $versionId)
                                  ->firstOrFail();

        // Update the version
        $version->update([
            'version' => $request->version,
            'release_date' => $request->release_date,
            'last_updated_date' => $request->last_updated_date,
            'updates' => json_encode($request->updates), // Convert array to JSON
        ]);

        return response()->json([
            'message' => 'Version updated successfully!',
            'version' => $version
        ], 200);
    }


    public function destroy($templateId, $versionId)
    {
        // Check if the template exists
        $template = Template::findOrFail($templateId);

        // Find the version to delete
        $version = TemplateVersion::where('template_id', $template->id)
                                ->where('id', $versionId)
                                ->firstOrFail();

        // Delete the version
        $version->delete();

        return response()->json([
            'message' => 'Version deleted successfully!'
        ], 200);
    }

}

