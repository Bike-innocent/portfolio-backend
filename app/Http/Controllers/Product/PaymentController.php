<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Http;
use App\Models\TemplateUser;
use App\Mail\TemplateDownloadMail;
use Illuminate\Support\Facades\Mail;
use Unicodeveloper\Paystack\Facades\Paystack; // ✅ Import Paystack
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function initializePayment(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'template_id' => 'required|exists:templates,id'
        ]);

        $template = Template::find($request->template_id);

        $paymentData = [
            'email' => $request->email,
            'amount' => $template->price * 100, // Paystack expects amount in kobo
            'currency' => 'NGN',
            'reference' => Paystack::genTranxRef(), // ✅ Uses Paystack correctly
            'callback_url' => route('paystack.callback'),
            'metadata' => [
                'template_id' => $template->id,
                'email' => $request->email
            ]
        ];

        return response()->json([
            'authorization_url' => Paystack::getAuthorizationUrl($paymentData)->url // ✅ Uses Paystack correctly
        ]);
    }





    public function verifyPayment(Request $request)
    {
        $reference = $request->query('reference');
        $paymentDetails = Paystack::getPaymentData();
    
        if ($paymentDetails['status'] === true) {
            $metadata = $paymentDetails['data']['metadata'];
            $email = $metadata['email'];
            $templateId = $metadata['template_id'];
    
            $template = Template::find($templateId);
    
            // ✅ Fetch private repo ZIP using GitHub API
            $githubToken = env('GITHUB_PERSONAL_ACCESS_TOKEN');
            $repoOwner = "Bike-innocent";
            $repoName = "portfolio"; // Replace with your private repo name
    
            $headers = [
                'Authorization' => "token $githubToken",
                'Accept' => 'application/vnd.github.v3+json'
            ];

            $apiUrl = "https://api.github.com/repos/$repoOwner/$repoName/tarball"; 

            try {
                $response = Http::withHeaders($headers)->get($apiUrl);
    
                if ($response->successful()) {
                    $downloadLink = $apiUrl; // ✅ Authenticated link
                } else {
                    Log::error("GitHub API Error: " . $response->body());
                    return redirect("http://localhost:5173/payment-failed?message=Error generating download link. Contact support.");
                }
            } catch (\Exception $e) {
                Log::error("GitHub API Exception: " . $e->getMessage());
                return redirect("http://localhost:5173/payment-failed?message=Internal Server Error. Contact support.");
            }
    
            // ✅ Store user purchase
            $existingPurchase = TemplateUser::where('email', $email)->where('template_id', $template->id)->exists();
            if (!$existingPurchase) {
                TemplateUser::create(['email' => $email, 'template_id' => $template->id]);
                $template->increment('downloads');
            }
    
            // ✅ Send email with secure download link
            Mail::to($email)->send(new TemplateDownloadMail($downloadLink, true));
    
            return redirect("http://localhost:5173/payment-success?message=Payment successful! Check your email for the download link.");
        }
    
        return redirect("http://localhost:5173/payment-failed?message=Payment failed! Try again.");
    }
    



    public function freeDownload(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'template_id' => 'required|exists:templates,id'
        ]);

        $template = Template::find($request->template_id);

        // Check if user already downloaded this template
        $existingDownload = TemplateUser::where('email', $request->email)
            ->where('template_id', $template->id)
            ->exists();

        if (!$existingDownload) {
            // Store user email in template_users table
            TemplateUser::create([
                'email' => $request->email,
                'template_id' => $template->id
            ]);

            // Increment download count in templates table
            $template->increment('downloads');
        }

        // Generate download link

        $downloadLink = "https://github.com/Bike-innocent/portfolio-backend/archive/refs/heads/main.zip"; // Replace with your actual repo link

        // Send Email


        // ✅ Send email for free template
        Mail::to($request->email)->send(new TemplateDownloadMail($downloadLink, false));

        return response()->json([
            'redirect_url' => "http://localhost:5173/successful?message=Check your email for the download link."
        ]);
    }
}
















    // public function verifyPayment(Request $request)
    // {
    //     $reference = $request->query('reference');
    //     $paymentDetails = Paystack::getPaymentData();

    //     if ($paymentDetails['status'] === true) {
    //         $metadata = $paymentDetails['data']['metadata'];
    //         $email = $metadata['email'];
    //         $templateId = $metadata['template_id'];

    //         $template = Template::find($templateId);
    //         $downloadLink = "https://github.com/Bike-innocent/portfolio/archive/refs/heads/main.zip"; // Replace with your actual repo link


    //         // ✅ Store user if not already purchased
    //         $existingPurchase = TemplateUser::where('email', $email)->where('template_id', $template->id)->exists();
    //         if (!$existingPurchase) {
    //             TemplateUser::create(['email' => $email, 'template_id' => $template->id]);
    //             $template->increment('downloads');
    //         }


    //         Mail::to($email)->send(new TemplateDownloadMail($downloadLink, true));



    //         return redirect("http://localhost:5173/payment-success?message=Payment successful! Check your email for the download link.");
    //     }

    //     return redirect("http://localhost:5173/payment-failed?message=Payment failed! Try again.");
    // }
