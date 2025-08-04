<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use App\Models\OrderTimeline;

class SendVendorEmail
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
    public function handle(OrderPlaced $event)
    {
        Mail::send('email.Order.order_recive_vendor', [
            'vendor_name' => $event->vendor->name,
            'order_id' => $event->order->id,
            'order_details' => $event->shopProducts,
            'shipping_charges' => $event->shopTotalShipment,
            'request' => $event->request
        ], function ($message) use ($event) {
            $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
            $message->to($event->vendor->email);
            $message->subject('New Order Received');
        });

        OrderTimeline::create([
            'seller_id' => $event->vendor->id,
            'order_id' => $event->order->id,
            'time_line' => 'Order confirmation email was sent to ' . $event->customer->name . ' (' . $event->customer->email . ').'
        ]);
    }
}
