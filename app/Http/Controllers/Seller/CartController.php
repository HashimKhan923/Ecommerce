<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Mail;
use App\Mail\CartDiscountMail;



class CartController extends Controller
{
    public function index($seller_id)
    {
        $data = Cart::with('product.product_single_gallery','varient','shipping','product.shop','product.discount','product.user','customer')->where('seller_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function giveDiscount(Request $request)
    {
        $cart = Cart::findOrFail($request->cart_id);

        $data = $request->validate([
            'discount_amount' => 'required|numeric|min:0',
        ]);

        $cart->update([
            'discount_amount' => $data['discount_amount'],
            'discount_reason' => 'manual',
            'discount_given_at' => now(),
        ]);

        // send email if customer exists
        if ($cart->customer && $cart->customer->email) {
            Mail::to($cart->customer->email)->send(new CartDiscountMail($cart));
        }

        return response()->json(['message' => 'Discount applied and email sent']);
    }

    public function notifyPriceDrop(Request $request)
    {
        $cart = Cart::findOrFail($request->cart_id);

        $newPrice = $request->input('new_price'); // e.g. from product update event
        $cart->update([
            'discount_amount' => $cart->total_amount - $newPrice,
            'discount_reason' => 'price_drop',
            'discount_given_at' => now(),
            'total_amount' => $newPrice,
        ]);

        if ($cart->customer && $cart->customer->email) {
            Mail::to($cart->customer->email)->send(new CartDiscountMail($cart));
        }

        return response()->json(['message' => 'Price drop email sent']);
    }


}
