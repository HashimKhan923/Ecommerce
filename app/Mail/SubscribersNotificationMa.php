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

   
    public $batchId;
    public $userId;

    public function __construct($batchId, $userId)
    {
        $this->batchId = $batchId;
        $this->userId = $userId;
    }

    public function build()
    {
        $trackingUrl = route('email.track.open', ['batchId' => $this->batchId, 'userId' => $this->userId->id]);
        $BatchId = $this->batchId;
        $UserId = $this->userId->id;
        return $this->from('no-reply@dragonautomart.com', 'Dragon Auto Mart')
        ->subject('Rev Up Your July 4th with Exclusive Parts Deals!')
        ->view('email.subscribers_email')
        ->with([
            'trackingUrl' => $trackingUrl,
            'batchId' => $BatchId,
            'userId' => $UserId
        ]);           
    }
}