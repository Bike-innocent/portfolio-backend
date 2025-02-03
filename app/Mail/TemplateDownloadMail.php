<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;



class TemplateDownloadMail extends Mailable
{
    use Queueable, SerializesModels;

    public $downloadLink;
    public $isPaid; // ✅ Add this property

    /**
     * Create a new message instance.
     */
    public function __construct($downloadLink, $isPaid)
    {
        $this->downloadLink = $downloadLink;
        $this->isPaid = $isPaid; // ✅ Store payment status
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Template Download Link')
                    ->markdown('emails.template_download', [
                        'downloadLink' => $this->downloadLink,
                        'isPaid' => $this->isPaid // ✅ Pass to view
                    ]);
    }
}
