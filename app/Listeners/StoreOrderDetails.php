<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\OrderDetail;
use App\Models\Product;

class StoreOrderDetails
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
        foreach ($event->shopProducts as $product) {
            $orderProduct = collect($event->request->products)->where('product_id', $product->id)->first();
            $sale = Product::with('product_single_gallery')->find($product->id);

            OrderDetail::create([
                'order_id' => $event->order->id,
                'product_id' => $product->id,
                'product_name' => $sale->name,
                'product_image' => $sale->product_single_gallery->image ?? '',
                'product_varient' => $orderProduct['product_varient'],
                'product_price' => $orderProduct['product_price'],
                'shipping_amount' => $orderProduct['shipping_amount'],
                'quantity' => $orderProduct['quantity'],
                'varient_id' => $orderProduct['varient_id'],
            ]);
        }
    }
}
