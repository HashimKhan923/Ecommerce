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

    public $user;
    public $batchId;

    public function __construct($user,$batchId)
    {
        $this->user = $user;
        $this->batchId = $batchId;
    }

    public function build()
    {
        $trackingUrl = route('email.track.open', ['batchId' => $this->batchId, 'userId' => $this->userId->id]);

        return $this->from('no-reply@dragonautomart.com', 'Dragon Auto Mart')
        ->subject('DAM | Grand Opening Sale')
        ->view('email.subscribers_email')
        ->with('trackingUrl', $trackingUrl);           
    }
}
