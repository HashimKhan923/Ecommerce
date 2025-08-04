<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\MyCustomer;
use Illuminate\Support\Facades\DB;

class UpdateCustomerSale
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
        $shopTotalAmount = $event->shopProducts->sum(function ($product) use ($event) {
            $productItems = collect($event->request->products)->where('product_id', $product->id);
            return $productItems->sum(function ($item) {
                return $item['product_price'] * $item['quantity'];
            });
        });

        MyCustomer::updateOrCreate(
            [
                'seller_id' => $event->vendor->id,
                'customer_id' => $event->customer->id,
            ],
            [
                'sale' => DB::raw("sale + {$shopTotalAmount}")
            ]
        );
    }
}
