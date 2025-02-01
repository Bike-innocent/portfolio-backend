<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Template;
use App\Models\TemplateUser;
use Unicodeveloper\Paystack\Facades\Paystack; // ✅ Import Paystack

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
        $paymentDetails = Paystack::getPaymentData(); // ✅ Get payment data
    
        if ($paymentDetails['status'] === true) {
            $metadata = $paymentDetails['data']['metadata'];
            $email = $metadata['email'];
            $templateId = $metadata['template_id'];
    
            $template = Template::find($templateId);
            $downloadLink = url("/download-template/{$templateId}");
    
            // ✅ Check if user already purchased this template
            $existingPurchase = TemplateUser::where('email', $email)
                ->where('template_id', $template->id)
                ->exists();
    
            if (!$existingPurchase) {
                // ✅ Store user email in template_users table
                TemplateUser::create([
                    'email' => $email,
                    'template_id' => $template->id
                ]);
    
                // ✅ Increment download count in templates table
                $template->increment('downloads');
            }
    
            // ✅ Send Email with Download Link
            Mail::raw("Thanks for your purchase. Download your template here: $downloadLink", function ($message) use ($email) {
                $message->to($email)->subject('Your Template Download Link');
            });
    
            // ✅ Redirect to frontend with success message in URL
            return redirect("http://localhost:5173/payment-success?message=Payment successful! Check your email for the download link.");
        }
    
        // Redirect to frontend with error message
        return redirect("http://localhost:5173/payment-failed?message=Payment failed! Please try again.");
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
        $downloadLink = url("/download-template/{$template->id}");
    
        // Send Email
        Mail::raw("Your free template is ready! Download here: $downloadLink", function ($message) use ($request) {
            $message->to($request->email)->subject('Your Free Template Download Link');
        });
    
        return response()->json([
            'redirect_url' => "http://localhost:5173/successful?message=Check your email for the download link."
        ]);
    }
    


    
}
