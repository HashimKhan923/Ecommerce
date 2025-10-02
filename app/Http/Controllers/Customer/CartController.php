<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;


class CartController extends Controller
{
    public function index($id)
    {
        $data = Cart::with('product.product_single_gallery','varient','shipping','product.shop','product.discount','product.user')->where('customer_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {

        $check = Cart::where('product_id',$request->product_id)->where('customer_id',$request->customer_id)->first();
        if($check)
        {
            return response()->json(['message'=>'already added in cart']);
        }
        $new = new Cart();
        $new->customer_id = $request->customer_id;
        $new->seller_id = $request->seller_id;
        $new->product_id = $request->product_id;
        $new->varient_id = $request->varient_id;
        $new->quantity = $request->quantity;
        $new->price = $request->price;
        $new->shipping_id = $request->shipping_id;
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
        Cart::where('customer_id',$customer_id)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);

    }

    public function cart_data(Request $request)
    {
        $Products = Product::with([
            'user',
            'category',
            'sub_category',
            'brand',
            'stock',
            'product_gallery',
            'discount',
            'tax',
            'shipping',
            'shop',
            'reviews',
            'product_varient'
        ])->whereIn('id', $request->ids)->get();
    
        return response()->json(['data'=>$Products]);
    }
}
