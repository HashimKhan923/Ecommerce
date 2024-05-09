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
use Illuminate\Support\Str;
use Mail;
use App\Models\User;


class OrderController extends Controller
{

    public function index($id)
    {
        $MyOrders = Order::with('order_detail.products.shop','order_detail.products.product_gallery','order_detail.products.category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.varient','order_detail.products.reviews.user','order_detail.products.tax','order_status','order_tracking','coupon_user.coupon')->where('customer_id',$id)->get();

        return response()->json(['MyOrders'=>$MyOrders]);
    }

public function create(Request $request)
{

  

    


    $productIds = [];
    foreach ($request->products as $product) {
        $productIds[] = $product['product_id'];
    }
    
    $products = Product::with('product_gallery')->whereIn('id', $productIds)->get();
    
    $productsByShop = $products->groupBy('shop_id');
    $orderIds = [];
    foreach ($productsByShop as $shopId => $shopProducts) {

        $vendorId = $shopProducts->first()->user_id;

        $vendor = User::find($vendorId);
        $customer = User::find($request->customer_id);


        $shopTotalAmount = $shopProducts->sum(function ($product) use ($request) {
            $orderProduct = collect($request->products)->where('product_id', $product->id)->first();
            return $orderProduct['product_price'] * $orderProduct['quantity'];
        });

        $shopTotalShipment = $shopProducts->sum(function ($product) use ($request) {
            $orderProduct = collect($request->products)->where('product_id', $product->id)->first();
            return $orderProduct['shipping_amount'];
        });
    
        $newOrder = new Order();
        $newOrder->order_code = Str::random(8) . '-' . Str::random(8);
        $newOrder->number_of_products = count($shopProducts);
        $newOrder->customer_id = $request->customer_id;
        $newOrder->shop_id = $shopId;
        $newOrder->sellers_id = $vendorId;
        if($request->coupon_id)
        {
            $newOrder->amount = $request->amount;
        }
        else
        {
            $newOrder->amount = $shopTotalAmount + $shopTotalShipment;
        }
        $newOrder->information = $request->information;
        $newOrder->stripe_payment_id = $request->payment_id;
        $newOrder->payment_method = $request->payment_method;
        $newOrder->payment_status = $request->payment_status;
        $newOrder->refund = $request->refund;
        $newOrder->save();

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

        Mail::send(
            'email.Order.order_recive_vendor',
            [
                'vendor_name' => $vendor->name,
                'order_id' => $newOrder->id,
                'order_details' => $shopProducts,
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


        if($request->coupon_id)
        {
            Coupon::where('id', $request->coupon_id)->increment('used');

            $CouponUser = new CouponUser();
            $CouponUser->coupon_id = $request->coupon_id;
            $CouponUser->user_id = $request->customer_id;
            $CouponUser->discount = $request->coupon_discount;
            $CouponUser->coupon_code = $request->coupon_code;
            $CouponUser->order_id = $newOrder->id;
            $CouponUser->save();
        }



        $my_customer = MyCustomer::where('seller_id',$vendorId)->where('customer_id',$request->customer_id)->first();

        if(!$my_customer)
        {
            $my_customer = new MyCustomer();
            $my_customer->seller_id = $vendorId;
            $my_customer->customer_id = $request->customer_id;
            $my_customer->sale = $shopTotalAmount;
        }
        else
        {
            $my_customer->sale = $my_customer->sale + $shopTotalAmount;
        }
        $my_customer->save();

        
        foreach ($shopProducts as $product) {
            $orderProduct = collect($request->products)->where('product_id', $product->id)->first();
            $sale = Product::with('product_single_gallery')->where('id', $product->id)->first();

            $newOrderDetail = new OrderDetail();
            $newOrderDetail->order_id = $newOrder->id;
            $newOrderDetail->product_id = $product->id;
            $newOrderDetail->product_name = $sale->name;
            $newOrderDetail->product_image = $sale->product_single_gallery->image;
            $newOrderDetail->product_varient = $orderProduct['product_varient'];;
            $newOrderDetail->product_price = $orderProduct['product_price'];
            $newOrderDetail->shipping_amount = $orderProduct['shipping_amount'];
            $newOrderDetail->quantity = $orderProduct['quantity'];
            $newOrderDetail->varient_id = $orderProduct['varient_id'];
            $newOrderDetail->save();

    
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

                $new = new FeaturedProductOrder();
                $new->order_id = $newOrder->id;
                $new->product_id = $product->id;
                $new->seller_id = $vendorId;
                $new->product_price = $orderProduct['product_price'];
                $new->quantity = $orderProduct['quantity'];
                $new->payment = $total;
                $new->save();

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
            'request' => $request
        ],
        function ($message) use ($user) {
            $message->from('support@dragonautomart.com','Dragon Auto Mart');
            $message->to($user->email);
            $message->subject('Order Confirmation');
        }
    );

    $MyOrders = Order::with('order_detail.products.shop','order_detail.products.product_gallery','order_detail.products.category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.products.product_varient','order_detail.products.reviews.user','order_detail.products.tax','order_detail.varient','order_status','order_tracking')->whereIn('id',$orderIds)->get();

    $orderIds = [];
    
    $response = ['status' => true, "message" => "Order Created Successfully!","data"=>$MyOrders];
    return response($response, 200);
}
}
