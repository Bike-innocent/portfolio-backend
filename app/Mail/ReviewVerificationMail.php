<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\TemplateReview;

class ReviewVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $review;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(TemplateReview $review)
    {
        $this->review = $review;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $approveUrl = route('reviews.approve', ['token' => $this->review->token]);
        $deleteUrl = route('reviews.delete', ['token' => $this->review->token]);

        return $this->subject('Verify Your Review')
            ->markdown('emails.reviews.verification')
            ->with([
                'reviewerName' => $this->review->reviewer_name,
                'approveUrl' => $approveUrl,
                'deleteUrl' => $deleteUrl,
            ]);
    }
}
