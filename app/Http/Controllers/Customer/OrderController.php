<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use Mail;

class OrderController extends Controller
{

    public function index($id)
    {
        $MyOrders = Order::with('order_detail')->where('customer_id',$id)->get();

        return response()->json(['MyOrders'=>$MyOrders]);
    }

public function create(Request $request)
{

    $productIds = [];
    foreach ($request->products as $product) {
        $productIds[] = $product['product_id'];
    }

    $products = Product::whereIn('id',$productIds)->get();

    // Group the products by vendor ID
    $productsByVendor = $products->groupBy('user_id');
    
    foreach ($productsByVendor as $vendorId => $vendorProducts) {
        $newOrder = new Order();
        $newOrder->order_code = Str::random(8) . '-' . Str::random(8);
        $newOrder->number_of_products = count($vendorProducts);
        $newOrder->customer_id = $request->customer_id;
        $newOrder->seller_id = $vendorId; // Set the vendor ID for each order
        $newOrder->amount = $request->amount; 
        $newOrder->information = $request->information;
        $newOrder->payment_method = $request->payment_method;
        $newOrder->payment_status = $request->payment_status;
        $newOrder->refund = $request->refund;
        $newOrder->save();
    
        foreach ($vendorProducts as $product) {
            $orderProduct = collect($request->products)->where('product_id', $product->id)->first();
            
            $newOrderDetail = new OrderDetail();
            $newOrderDetail->order_id = $newOrder->id;
            $newOrderDetail->product_id = $product->id;
            $newOrderDetail->quantity = $orderProduct['quantity'];
            $newOrderDetail->varient = $request->varient;
            $newOrderDetail->save();
        }
    }

    // Mail::send(
    //     'email.password-reset',
    //     [
    //         'token'=>$token,
    //         'name'=>$query->name,
    //         //'last_name'=>$query->last_name
    //     ], 
    
    // function ($message) use ($query) {
    //     $message->from(env('MAIL_USERNAME'));
    //     $message->to($query->email);
    //     $message->subject('Forget Password');
    // });

    $response = ['status' => true, "message" => "Order Created Successfully!"];
    return response($response, 200);
}
}
