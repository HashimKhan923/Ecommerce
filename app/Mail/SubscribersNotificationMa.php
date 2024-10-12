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

        Mail::send(
            'email.subscribers_email',
            [
                'details' => $this->details
            ],
            function ($message) use ($user) { 
                $message->from('no-reply@dragonautomart.com','Dragon Auto Mart');
                $message->subject('DAM | Grand Opening Sale');
            }
        );

        // return $this->from('khanhash1994@gmail.com', 'Dragon Auto Mart')
        // ->subject('DAM | Grand Opening Sale')
        // ->view('email.subscribers_email')
        // ->with('details', $this->details);            
    }
}
