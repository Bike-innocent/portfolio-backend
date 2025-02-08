<?php



namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\TemplateUser;
use App\Mail\TemplateDownloadMail;
use Illuminate\Support\Facades\Mail;
use Unicodeveloper\Paystack\Facades\Paystack;
use Illuminate\Support\Facades\Storage;
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
            'reference' => Paystack::genTranxRef(),
            'callback_url' => route('paystack.callback'),
            'metadata' => [
                'template_id' => $template->id,
                'email' => $request->email
            ]
        ];

        return response()->json([
            'authorization_url' => Paystack::getAuthorizationUrl($paymentData)->url
        ]);
    }




    public function verifyPayment(Request $request)
    {
        $reference = $request->query('reference');
        $paymentDetails = Paystack::getPaymentData();

        if ($paymentDetails['status'] === true) {
            Log::info('Payment successful', $paymentDetails);

            $metadata = $paymentDetails['data']['metadata'];
            $email = $metadata['email'] ?? null;
            $templateId = $metadata['template_id'] ?? null;

            Log::info("Extracted metadata - Email: {$email}, Template ID: {$templateId}");

            if (!$email || !$templateId) {
                Log::error("Missing metadata in payment response");
                return redirect("https://chibuikeinnocent.tech/payment-failed?message=Missing payment metadata.");
            }

            $template = Template::find($templateId);
            if (!$template) {
                Log::error("Template not found for ID: {$templateId}");
                return redirect("https://chibuikeinnocent.tech/payment-failed?message=Template not found.");
            }

            $filePath = "templates/{$template->file_path}";

            if (!Storage::exists($filePath)) {
                Log::error("File does not exist: {$filePath}");
                return redirect("https://chibuikeinnocent.tech/payment-failed?message=File not found.");
            }

            $downloadLink = route('download.template', [
                'template' => $template->file_path,
                'expires' => now()->addMinutes(1000)->timestamp,
                'signature' => hash_hmac('sha256', $template->file_path, env('APP_KEY'))
            ]);

            if (!TemplateUser::where('email', $email)->where('template_id', $template->id)->exists()) {
                TemplateUser::create(['email' => $email, 'template_id' => $template->id]);
                $template->increment('downloads');
            }

            Log::info("Sending email to: {$email} with link: {$downloadLink}");
            Mail::to($email)->send(new TemplateDownloadMail($downloadLink, true));

            return redirect("https://chibuikeinnocent.tech/payment-success?message=Payment successful! Check your email for the download link.");
        }

        Log::error("Payment failed", $paymentDetails);
        return redirect("https://chibuikeinnocent.tech/payment-failed?message=Payment failed! Try again.");
    }

    public function freeDownload(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'template_id' => 'required|exists:templates,id'
        ]);

        $template = Template::find($request->template_id);
        $filePath = "templates/{$template->file_path}";

        if (Storage::exists($filePath)) {
            $downloadLink = route('download.template', [
                'template' => $template->file_path,
                'expires' => now()->addMinutes(1000)->timestamp,
                'signature' => hash_hmac('sha256', $template->file_path, env('APP_KEY'))
            ]);

            if (!TemplateUser::where('email', $request->email)->where('template_id', $template->id)->exists()) {
                TemplateUser::create(['email' => $request->email, 'template_id' => $template->id]);
                $template->increment('downloads');
            }

            Mail::to($request->email)->send(new TemplateDownloadMail($downloadLink, false));
        }

        return response()->json([
            'redirect_url' => "https://chibuikeinnocent.tech/successful?message=Check your email for the download link."
        ]);
    }


    

    public function uploadTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'file' => 'required|mimes:zip|max:20480' // Accepts only zip files (Max: 20MB)
        ]);

        $template = Template::findOrFail($request->template_id);

        // Store file in storage/app/templates
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $path = $file->storeAs('templates', $fileName);

        // Check if file is successfully stored
        if (!Storage::exists($path)) {
            return response()->json(['error' => 'File upload failed'], 500);
        }

        // Save only the filename in the database
        $template->update(['file_path' => $fileName]);

        return response()->json([
            'message' => 'Template uploaded successfully!',
            'file_path' => $fileName
        ]);
    }






    public function downloadTemplate(Request $request, $template)
    {
        $expectedSignature = hash_hmac('sha256', $template, env('APP_KEY'));

        if ($request->query('signature') !== $expectedSignature || now()->timestamp > $request->query('expires')) {
            abort(403, "Unauthorized Access");
        }

        // Use Laravel Storage helper
        $filePath = "templates/{$template}";

        if (!Storage::exists($filePath)) {
            return response()->json(['error' => 'File Not Found'], 404);
        }

        // return Storage::download($filePath);
        return response()->download(storage_path("app/{$filePath}"));
    }
}












