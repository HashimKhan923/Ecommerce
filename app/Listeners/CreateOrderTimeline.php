<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\OrderTimeline;
use App\Models\Shop;

class CreateOrderTimeline
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
        $shop = Shop::find($event->order->shop_id);

        OrderTimeline::insert([
            [
                'seller_id' => $event->vendor->id,
                'customer_id' => $event->customer->id,
                'order_id' => $event->order->id,
                'time_line' => $event->order->amount . ' USD was captured using a ' . $event->request->payment_method . '.'
            ],
            [
                'seller_id' => $event->vendor->id,
                'customer_id' => $event->customer->id,
                'order_id' => $event->order->id,
                'time_line' => $event->customer->name . ' placed this order on ' . $shop->name . ' checkout (#' . $event->order->id . ')'
            ],
            [
                'seller_id' => $event->vendor->id,
                'customer_id' => $event->customer->id,
                'order_id' => $event->order->id,
                'time_line' => 'Confirmation ' . $event->order->order_code . ' was generated for this order'
            ]
        ]);
    }
}
