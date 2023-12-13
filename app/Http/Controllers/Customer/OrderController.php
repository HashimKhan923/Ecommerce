<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\Stock;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use Mail;
use App\Models\User;

class OrderController extends Controller
{

    public function index($id)
    {
        $MyOrders = Order::with('order_detail.products.product_gallery','order_detail.products.category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.products.product_varient','order_detail.products.reviews.user','order_detail.products.tax','order_status')->where('customer_id',$id)->get();

        return response()->json(['MyOrders'=>$MyOrders]);
    }

public function create(Request $request)
{

    $productIds = [];
    foreach ($request->products as $product) {
        $productIds[] = $product['product_id'];
    }

    $products = Product::with('product_gallery')->whereIn('id',$productIds)->get();

    // Group the products by vendor ID
    $productsByVendor = $products->groupBy('user_id');
    
    foreach ($productsByVendor as $vendorId => $vendorProducts) {
        $newOrder = new Order();
        $newOrder->order_code = Str::random(8) . '-' . Str::random(8);
        $newOrder->number_of_products = count($vendorProducts);
        $newOrder->customer_id = $request->customer_id;
        $newOrder->seller_id = $vendorId; 
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
            $newOrderDetail->product_price = $orderProduct['product_price'];
            $newOrderDetail->quantity = $orderProduct['quantity'];
            $newOrderDetail->varient = $request->varient;
            $newOrderDetail->save();

            $VarientStock = ProductVarient::where('product_id',$product->id)->first();
            $sale = Product::where('id',$product->id)->first();
            $stock = Stock::where('product_id',$product->id)->first();
            if($VarientStock)
            {
                $VarientStock->stock = $VarientStock->stock - $orderProduct['quantity'];
                $VarientStock->save();
            }
            else
            {
                $stock->stock = $stock->stock - $orderProduct['quantity'];
                $stock->save();
            }

            $sale->num_of_sale = $sale->num_of_sale + $orderProduct['quantity'];
            $sale->save();

        }
    }

    $user = User::where('id',$request->customer_id)->first();


    Mail::send(
        'email.Order.order_recive',
        [
            'buyer_name' => $user->name,
            'productsByVendor' => $productsByVendor,
            'request' => $request
        ],
        function ($message) use ($user) { // Add $user variable here
            $message->from('support@dragonautomart.com','Dragon Auto Mart');
            $message->to($user->email);
            $message->subject('Order Confirmation');
        }
    );
    
    $response = ['status' => true, "message" => "Order Created Successfully!"];
    return response($response, 200);
}
}
