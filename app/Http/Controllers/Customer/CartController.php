<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    public function index($id)
    {
        $data = Cart::where('customer_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Cart();
        $new->customer_id = $request->customer_id;
        $new->product_id = $request->product_id;
        $new->quantity = $request->quantity;
        $new->save();

        return response()->json(['message'=>'add to cart successfully!',200]);
    }

    public function update(Request $request)
    {
        $update = Cart::find($request->cart_id);
        $update->quantity = $request->quantity;
        $update->save();

        return response()->json(['message'=>'quantity updated successfully!',200]);
    }

    public function delete($cart_id)
    {
        Cart::find($cart_id)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);

    }

    public function clear($customer_id)
    {
        Cart::find($customer_id)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);

    }
}