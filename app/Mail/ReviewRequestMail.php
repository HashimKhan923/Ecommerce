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

    public $order_detail;

    public function __construct(OrderDetail $order_detail)
    {
        $this->order_detail = $order_detail;
    }

    public function build()
    {
        return $this->subject('Product Review')
                    ->view('emails.review_request');
    }
}
