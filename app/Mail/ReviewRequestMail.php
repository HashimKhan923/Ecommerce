<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\OrderDetail;

class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $order_detail;

    public function __construct(Order $order, OrderDetail $order_detail)
    {
        $this->order = $order;
        $this->order_detail = $order_detail;
    }

    public function build()
    {
        return $this->subject('Product Review')
                    ->view('email.Order.review_request')
                    ->with([
                        'order' => $this->order,
                        'order_detail' => $this->order_detail
                    ]);
    }

}
