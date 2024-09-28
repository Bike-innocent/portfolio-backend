<?php

namespace App\Http\Controllers\posts;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Post;
use App\Models\ReportReason;
use App\Models\Comment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Fetch reports with related data
        $reports = Report::with(['reporter', 'reportedUser', 'post', 'comment.user', 'reason'])->get();

        return response()->json($reports);
    }



    // Method for reporting a comment
    public function reportComment(Request $request)
    {
        $validated = $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'reason_id' => 'required|exists:report_reasons,id',
            'additional_info' => 'nullable|string',
        ]);

        // Fetch the comment and the associated post
        $comment = Comment::findOrFail($validated['comment_id']);
        $post = $comment->post;

        // Determine the reported user's ID
        $reportedUserId = $comment->user_id;

        $report = Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $reportedUserId,
            'post_id' => $post->id,  // Automatically associate the post with the comment
            'comment_id' => $validated['comment_id'],
            'reason_id' => $validated['reason_id'],
            'additional_info' => $validated['additional_info'] ?? null,
        ]);

        return response()->json(['message' => 'Comment report submitted successfully', 'report' => $report], 201);
    }

    // Method for reporting a post
    public function reportPost(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'reason_id' => 'required|exists:report_reasons,id',
            'additional_info' => 'nullable|string',
        ]);

        // Fetch the post
        $post = Post::findOrFail($validated['post_id']);

        // Determine the reported user's ID
        $reportedUserId = $post->user_id;

        $report = Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $reportedUserId,
            'post_id' => $validated['post_id'],  // Associate the post
            'comment_id' => null,  // No comment is associated
            'reason_id' => $validated['reason_id'],
            'additional_info' => $validated['additional_info'] ?? null,
        ]);

        return response()->json(['message' => 'Post report submitted successfully', 'report' => $report], 201);
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Report deleted successfully.'], 200);
    }
}
