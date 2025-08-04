<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\FeaturedProductOrder;
use App\Models\Product;
use App\Models\Shop;

class TrackFeaturedProducts
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
            $sale = Product::find($product->id);

            if ($product->featured) {
                $total = $orderProduct['product_price'] * 0.1 * $orderProduct['quantity'];
                FeaturedProductOrder::create([
                    'order_id' => $event->order->id,
                    'product_id' => $product->id,
                    'seller_id' => $event->vendor->id,
                    'product_price' => $orderProduct['product_price'],
                    'quantity' => $orderProduct['quantity'],
                    'payment' => $total,
                ]);
            }

            $shopModel = Shop::where('seller_id', $sale->user_id)->first();
            $shopModel->increment('sold_products', $orderProduct['quantity']);
            $sale->increment('num_of_sale', $orderProduct['quantity']);
        }
    }
}
