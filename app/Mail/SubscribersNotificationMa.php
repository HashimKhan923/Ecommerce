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
    public $batchId;

    public function __construct($details, $batchId)
    {
        $this->details = $details;
        $this->batchId = $batchId;
    }

    public function build()
    {
        $trackingUrl = route('email.track.open', ['batchId' => $this->batchId]);

        return $this->from('no-reply@dragonautomart.com', 'Dragon Auto Mart')
        ->replyTo('support@dragonautomart.com')
        ->subject('DAM | Grand Opening Sale')
        ->view('email.subscribers_email')
        ->with('details', $this->details)
        ->with('trackingPixel', $trackingUrl);          
    }
}
