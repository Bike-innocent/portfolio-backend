<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TemplateReview; // Update with your model namespace
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewVerificationMail;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:500',
        ]);

        $token = Str::random(32); // Generate a unique token

        $review = TemplateReview::create([
            'template_id' => $request->template_id,
            'reviewer_name' => $request->reviewer_name,
            'reviewer_email' => $request->reviewer_email,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'token' => $token,
            'status' => 'pending',
        ]);

        // Optionally send an email (we'll implement this later)
        Mail::to($request->reviewer_email)->send(new ReviewVerificationMail($review));

        return response()->json(['message' => 'Review submitted. Please check your email to verify.']);
    }

    public function approve($token)
    {
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
    
        $review = TemplateReview::where('token', $token)->first();
    
        if (!$review) {
            return redirect($frontendUrl . '/verify?status=error&message=Invalid+token+or+review+already+handled');
        }
    
        $review->update(['status' => 'approved']);
    
        return redirect($frontendUrl . '/verify?status=success&message=Review+has+been+approved');
    }
    
    public function delete($token)
    {
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173'));
    
        $review = TemplateReview::where('token', $token)->first();
    
        if (!$review) {
            return redirect($frontendUrl . '/verify?status=error&message=Invalid+token+or+review+already+handled');
        }
    
        $review->delete();
    
        return redirect($frontendUrl . '/verify?status=success&message=Review+has+been+deleted');
    }


    public function destroy($id)
    {
    
        $review = TemplateReview::where('id', $id)->first();
    
        if (!$review) {
            return response()->json(['message' => 'review not found'], 404);
        }
    
        $review->delete();
    
        return response()->json(['message' => 'review deleted'], 200);
    }

 
    
}
