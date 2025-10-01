<?php

namespace App\Listeners;

use App\Events\ProductPriceUpdated;
use App\Mail\CartDiscountMail;
use App\Models\Cart;
use Illuminate\Support\Facades\Mail;

class NotifyCartPriceDrop
{
    public function handle(ProductPriceUpdated $event)
    {
        // only trigger if price actually decreased
        if ($event->newPrice >= $event->oldPrice) {
            return;
        }

        // find all incomplete carts that contain this product
        $carts = Cart::where('status', 'incomplete')->get();

        foreach ($carts as $cart) {
            $items = collect($cart->items);

            $item = $items->firstWhere('product_id', $event->product->id);

            if ($item) {
                // recalc cart total
                $oldItemTotal = $item['price'] * $item['qty'];
                $newItemTotal = $event->newPrice * $item['qty'];

                $cart->update([
                    'discount_amount' => $cart->discount_amount + ($oldItemTotal - $newItemTotal),
                    'discount_reason' => 'price_drop',
                    'discount_given_at' => now(),
                    'total_amount' => $cart->total_amount - ($oldItemTotal - $newItemTotal),
                ]);

                // update the cart item price in items JSON
                $items = $items->map(function ($i) use ($event) {
                    if ($i['product_id'] == $event->product->id) {
                        $i['price'] = $event->newPrice;
                    }
                    return $i;
                });
                $cart->items = $items;
                $cart->save();

                // send email
                if ($cart->customer && $cart->customer->email) {
                    Mail::to($cart->customer->email)->send(new CartDiscountMail($cart));
                }
            }
        }
    }
}
