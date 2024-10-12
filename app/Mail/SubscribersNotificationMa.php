<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Mail;

class SubscribersNotificationMa extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // Use the 'no_reply' mailer for sending this email
        Mail::mailer('no_reply')->send('email.subscribers_email', ['details' => $this->details], function ($message) {
            $message->from('no-reply@dragonautomart.com', 'Dragon Auto Mart');
            $message->subject('DAM | Grand Opening Sale');
        });

        return;
    }
}
