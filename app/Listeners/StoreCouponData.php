<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\CouponUser;


class StoreCouponData
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
        if ($event->request->filled('coupon_discount')) {
            CouponUser::create([
                'discount' => $event->request->coupon_discount,
                'order_id' => $event->order->id
            ]);
        }
    }

}
