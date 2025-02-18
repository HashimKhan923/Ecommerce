<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\OrderDetail;

class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(OrderDetail $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Review Your Purchase on Dragon Auto Mart')
                    ->view('emails.review_request');
    }
}
