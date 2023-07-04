<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Support\Str;

class OrderController extends Controller
{

    public function index($id)
    {
        $MyOrders = Order::with('order_detail')->where('customer_id',$id)->get();

        return response()->json(['MyOrders'=>$MyOrders]);
    }

    public function create(Request $request)
    {
        $products = Product::whereIn('id', $request->product_id)->get();

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
            $newOrder->refund = $request->refund;
            $newOrder->save();
        
            foreach ($vendorProducts as $product) {
                $newOrderDetail = new OrderDetail();
                $newOrderDetail->order_id = $newOrder->id;
                $newOrderDetail->product_id = $product->id;
                $newOrderDetail->quantity = $request->quantity;
                $newOrderDetail->save();
            }
        }

        $response = ['status'=>true,"message" => "Order Created Successfully!"];
        return response($response, 200);

    }
}
