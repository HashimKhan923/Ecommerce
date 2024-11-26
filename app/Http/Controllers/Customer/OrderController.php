<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\Stock;
use App\Models\Shop;
use App\Models\OrderDetail;
use App\Models\FeaturedProductOrder;
use App\Models\MyCustomer;
use App\Models\CouponUser;
use App\Models\Coupon;
use App\Models\OrderTimeline;
use App\Models\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\User;


class OrderController extends Controller
{

    public function index($id)
    {
        $MyOrders = Order::with('order_detail.products.shop','order_detail.products.product_gallery','order_detail.products.category','order_detail.products.sub_category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.varient','order_detail.products.reviews.user','order_detail.products.tax','order_status','order_tracking','coupon_user.coupon','order_refund')->where('customer_id',$id)->get();

        return response()->json(['MyOrders'=>$MyOrders]);
    }

    public function detail($id)
    {
        $data = Order::with('order_detail.products.product_gallery','order_detail.products.category','order_detail.products.sub_category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.varient','order_detail.products.reviews.user','order_detail.products.tax','order_detail.products.shop.shop_policy','order_status','order_tracking','order_refund','shop','nagative_payout_balance','coupon_user.coupon','order_timeline')->where('id',$id)->first();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {

            $productIds = collect($request->products)->pluck('product_id')->toArray();
        
            $products = Product::with('product_gallery')->whereIn('id', $productIds)->get();
            
            $productsByShop = $products->groupBy('shop_id');
            $orderIds = [];
            $TotalShippingAmount = 0.00;
            foreach ($productsByShop as $shopId => $shopProducts) {
        
                $vendorId = $shopProducts->first()->user_id;
        
                $vendor = User::find($vendorId);
                $customer = User::find($request->customer_id);
        
        
                // Calculating shop total amount and total shipment
                $shopTotalAmount = $shopProducts->sum(function ($product) use ($request) {
                    return collect($request->products)->where('product_id', $product->id)->sum('product_price') * collect($request->products)->where('product_id', $product->id)->sum('quantity');
                });
        
                $shopTotalShipment = $shopProducts->sum(function ($product) use ($request) {
                    return collect($request->products)->where('product_id', $product->id)->sum('shipping_amount');
                });

                $TotalShippingAmount += $shopTotalShipment;
            
                $newOrder = Order::create([
                    'order_code' => Str::random(8) . '-' . Str::random(8),
                    'number_of_products' => count($shopProducts),
                    'customer_id' => $request->customer_id,
                    'shop_id' => $shopId,
                    'sellers_id' => $vendorId,
                    'amount' => $request->amount,
                    'tax'=> $request->tax,
                    'signature'=> $request->signature,
                    'insurance'=> $request->insurance,
                    'information' => $request->information,
                    'stripe_payment_id' => $request->payment_id,
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_status,
                    'refund' => $request->refund,
                ]);
        
                $shop = Shop::find($shopId);
        
                OrderTimeline::create([
                    'seller_id' => $vendorId,
                    'customer_id' => $request->customer_id,
                    'order_id' => $newOrder->id,
                    'time_line' => $newOrder->amount .' USD was captured using a '.$request->payment_method.'.'
                ]);
        
                OrderTimeline::create([
                    'seller_id' => $vendorId,
                    'customer_id' => $request->customer_id,
                    'order_id' => $newOrder->id,
                    'time_line' => $customer->name .' placed this order on '.$shop->name.' checkout(#'.$newOrder->id.')'
                ]);
        
                OrderTimeline::create([
                    'seller_id' => $vendorId,
                    'customer_id' => $request->customer_id,
                    'order_id' => $newOrder->id,
                    'time_line' => 'Confirmation '.$newOrder->order_code.' was genereated for this order'
                ]);
        
                Notification::create([
                    'customer_id' => $vendorId,
                    'notification' => 'new order #'.$newOrder->id.' received'
                ]);
        
                Mail::send(
                    'email.Order.order_recive_vendor',
                    [
                        'vendor_name' => $vendor->name,
                        'order_id' => $newOrder->id,
                        'order_details' => $shopProducts,
                        'shipping_charges' => $shopTotalShipment,
                        'request' => $request
                    ],
                    function ($message) use ($vendor,$newOrder,$customer) {
                        $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                        $message->to($vendor->email);
                        $message->subject('New Order Received');
        
                        OrderTimeline::create([
                            'seller_id' => $vendor->id,
                            'order_id' => $newOrder->id,
                            'time_line' => 'order confirmation email was sent to '.$customer->name.'  ('.$customer->email.').'
                        ]);
                    }
                    
                );
        
                $orderIds[] = $newOrder->id;
        
        
                // if($request->coupon_id)
                // {
                //     Coupon::where('id', $request->coupon_id)->increment('used');
        
                    CouponUser::create([
                        // 'coupon_id' => $request->coupon_id,
                        // 'user_id' => $request->customer_id,
                        'discount' => $request->coupon_discount,
                        // 'coupon_code' => $request->coupon_code,
                        'order_id' => $newOrder->id
                    ]);
        
                // }
        
        
        
                $my_customer = MyCustomer::where('seller_id',$vendorId)->where('customer_id',$request->customer_id)->first();
        
                if(!$my_customer)
                {
                    MyCustomer::create([
                        'seller_id' => $vendorId,
                        'customer_id' => $request->customer_id,
                        'sale' => $shopTotalAmount
                    ]);
        
                }
                else
                {
                    $my_customer->sale = $my_customer->sale + $shopTotalAmount;
                    $my_customer->save();
                }
                
        
                
                foreach ($shopProducts as $product) {
                    $orderProduct = collect($request->products)->where('product_id', $product->id)->first();
                    $sale = Product::with('product_single_gallery')->where('id', $product->id)->first();
        
                    OrderDetail::create([
                        'order_id' => $newOrder->id,
                        'product_id' => $product->id,
                        'product_name' => $sale->name,
                        'product_image' => $sale->product_single_gallery->image,
                        'product_varient' => $orderProduct['product_varient'],
                        'product_price' => $orderProduct['product_price'],
                        'shipping_amount' => $orderProduct['shipping_amount'],
                        'quantity' => $orderProduct['quantity'],
                        'varient_id' => $orderProduct['varient_id'],
                    ]);
        
                    
        
            
                    $VarientStock = ProductVarient::where('product_id', $product->id)->first();
                    
                    $stock = Stock::where('product_id', $product->id)->first();
        
        
                    if ($VarientStock) {
                        $VarientStock->stock = $VarientStock->stock - $orderProduct['quantity'];
                        $VarientStock->save();
                    } 
                    if($stock) {
                        $stock->stock = $stock->stock - $orderProduct['quantity'];
                        $stock->save();
                    }
        
                    $featured_product = Product::where('id',$product->id)->where('featured',1)->first();
        
                    if($featured_product)
                    {
                        $tenPercent = $orderProduct['product_price'] * 0.1;
                        $total = $tenPercent * $orderProduct['quantity'];
        
                        FeaturedProductOrder::create([
                            'order_id' => $newOrder->id,
                            'product_id' => $product->id,
                            'seller_id' => $vendorId,
                            'product_price' => $orderProduct['product_price'],
                            'quantity' => $orderProduct['quantity'],
                            'payment' => $total,
                        ]);
        
                        
        
                    }
        
                    $sold_product = Shop::where('seller_id',$sale->user_id)->first();
                    $sold_product->sold_products = $sold_product->sold_products + $orderProduct['quantity'];
                    $sold_product->save();
            
                    $sale->num_of_sale = $sale->num_of_sale + $orderProduct['quantity'];
                    $sale->save();
                }
            }
        
            $user = User::where('id',$request->customer_id)->first();
        
        
            Mail::send(
                'email.Order.order_recive',
                [
                    'buyer_name' => $user->name,
                    'productsByVendor' => $productsByShop,
                    'TotalShippingAmount' => $TotalShippingAmount,
                    'request' => $request
                ],
                function ($message) use ($user,$request) {
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($request->information[7]);
                    $message->subject('Order Confirmation');
                }
            );
        
            $MyOrders = Order::with('order_detail.products.shop','order_detail.products.product_gallery','order_detail.products.category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.products.product_varient','order_detail.products.reviews.user','order_detail.products.tax','order_detail.varient','order_status','order_tracking')->whereIn('id',$orderIds)->get();
        
            $orderIds = [];
            
            $response = ['status' => true, "message" => "Order Created Successfully!","data"=>$MyOrders];
            return response($response, 200);


    }
}
