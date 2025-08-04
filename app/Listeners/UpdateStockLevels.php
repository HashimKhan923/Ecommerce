<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ProductVarient;
use App\Models\Stock;
use App\Models\Product;

class UpdateStockLevels
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

            ProductVarient::where('product_id', $product->id)
                ->decrement('stock', $orderProduct['quantity']);

            Stock::where('product_id', $product->id)
                ->decrement('stock', $orderProduct['quantity']);
        }
    }

}
