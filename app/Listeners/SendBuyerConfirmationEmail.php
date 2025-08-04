<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBuyerConfirmationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BuyerOrderPlaced $event)
    {
        Mail::send('email.Order.order_recive', [
            'buyer_name' => $event->user->name,
            'productsByVendor' => $event->productsByShop,
            'TotalShippingAmount' => $event->TotalShippingAmount,
            'request' => $event->request
        ], function ($message) use ($event) {
            $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
            $message->to($event->request->information[7]);
            $message->subject('Order Confirmation');
        });
    }
}
