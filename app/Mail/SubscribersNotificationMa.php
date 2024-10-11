<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

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
        return $this->from('support@dragonautomart.com', 'Dragon Auto Mart')
        ->replyTo('no-reply@dragonautomart.com', 'Dragon Auto Mart')
        ->subject('DAM | Grand Opening Sale')
        ->view('email.subscribers_email')
        ->with('details', $this->details);
    }
}
