<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payout;
use App\Models\User;
use App\Models\Shop;
use App\Models\OrderStatus;
use Carbon\Carbon;
use App\Models\OrderTracking;
use App\Models\Notification;
use App\Models\FeaturedProductOrder;
use App\Models\ProductListingPayment;
use App\Models\NagativePayoutBalance;
use App\Models\OrderTimeline;
use Mail;

class OrderController extends Controller
{
    public function index($id)
    {
        $data = Order::with('order_detail.products.product_gallery','order_detail.products.category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.varient','order_detail.products.reviews.user','order_detail.products.tax','order_detail.products.shop.shop_policy','order_status','order_tracking','order_refund','shop','nagative_payout_balance','coupon_user.coupon','order_timeline')->where('sellers_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function delivery_status(Request $request)
    {
        $order = Order::with('order_detail.products.product_gallery')->where('id',$request->id)->first();
        $user = User::where('id',$order->customer_id)->first();
        $seller = User::where('id',$order->sellers_id)->first();
        $shop = Shop::where('seller_id',$order->sellers_id)->first();
        $TrackingNumber = '';
        if($request->delivery_status == 'Delivered')
        {

            $order->shipping_amount = $request->shipping_amount;
            $order->save();

            OrderStatus::create([
                'order_id' => $request->id,
                'status' => 'deliverd'
            ]);


            OrderTimeline::create([
                'seller_id' => $seller->id,
                'customer_id' => $order->customer_id,
                'order_id' => $request->id,
                'time_line' => 'order status changed to delivered'
            ]);

          $TrackingOrder = OrderTracking::create([
                'order_id' => $order->id,
                'tracking_number' => $request->tracking_number,
                'courier_name' => $request->courier_name,
                'courier_link' => $request->courier_link,
                'shipping_label' => $request->shipping_label,
            ]);


            



            Mail::send(
                'email.Order.order_completed',
                [
                    'buyer_name' => $user->name,
                    'shop' => $shop,
                    'order'=> $order,
                    'TrackingOrder' => $TrackingOrder,
                    'date' => Carbon::today()->toDateString()
                ],
                function ($message) use ($user) { 
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );

            OrderTimeline::create([
                'seller_id' => $seller->id,
                'customer_id' => $order->customer_id,
                'order_id' => $request->id,
                'time_line' => 'order completion email sent to customer'
            ]);


            
          $NewPayout = Payout::create([
                'date' => Carbon::now(),
                'seller_id' => $order->sellers_id,
                'shop_id' => $order->shop_id,
                'order_id' => $order->id,
            ]);

            
            
            $orderAmountInCents = $order->amount * 100; 
            
            $firstCommissionRate = 0.04;
            $secondCommissionRate = 0; 
            if ($seller->created_at < Carbon::now()->subMonths(3)) {
                $secondCommissionRate = 0.05;
            }
            
            $percentageDeduction = $orderAmountInCents * $firstCommissionRate;
            $fixedDeduction = 40;
            $totalDeduction = sprintf("%.2f", $percentageDeduction) + $fixedDeduction;
            
            $totalDeduction += $orderAmountInCents * sprintf("%.2f", $secondCommissionRate);
            
            $adjustedAmountInCents = $orderAmountInCents - $totalDeduction;
            $adjustedAmountInDollars = $adjustedAmountInCents / 100;
            
            $featuredAmount = FeaturedProductOrder::where('order_id', $order->id)->where('payment_status','unpaid')->sum('payment') ?? 0.00;


            $ListingPayment = 0;
            $ProductListingPayment = ProductListingPayment::where('seller_id', $order->sellers_id)
            ->where('payment_status', 'unpaid')
            ->first();
            if ($ProductListingPayment) {
                $ProductListingPayment->payment_status = 'paid';
                $ProductListingPayment->save();

                $ListingPayment = $ProductListingPayment->listing_amount;
            
            $NewPayout->product_listing_id = $ProductListingPayment->id;
            }
            $NewPayout->platform_fee = $totalDeduction;
            $NewPayout->commission = $firstCommissionRate + $secondCommissionRate;
            $nn =  $order->shipping_amount + $featuredAmount + $ListingPayment;
            $NewPayout->amount = $adjustedAmountInDollars - $nn;



            $NewPayout->save();

            OrderTimeline::create([
                'seller_id' => $seller->id,
                'customer_id' => $order->customer_id,
                'order_id' => $request->id,
                'time_line' => 'Successfully created new payout: $'.$NewPayout->amount.' USD.'
            ]);


            if($featuredAmount > 0)
            {
                FeaturedProductOrder::where('order_id', $order->id)->update(['payment_status'=>'paid']);

            }


            Notification::create([
                'customer_id' => $user->id,
                'notification' => 'your order #'.$order->id.'has been ready to delivered',
            ]);

            


        }
        else
        {

          $Tracking = OrderTracking::where('order_id',$request->id)->first();

          if($Tracking->shipping_label != null)
          {
            $TrackingNumber = $Tracking->tracking_number;
          }
          $Tracking->delete();
            Payout::where('order_id',$request->id)->delete();
            OrderStatus::where('order_id',$request->id)->delete();
            // NagativePayoutBalance::where('order_id',$request->id)->delete();
            Order::where('id',$order->id)->update(['shipping_amount'=> 0]);

            $FeaturedProductOrder = FeaturedProductOrder::where('order_id', $order->id)
            ->where('payment_status', 'paid')
            ->latest()
            ->first();

            if ($FeaturedProductOrder) {
                
                $FeaturedProductOrder->update([
                    'payment_status' => 'unpaid'
                ]);
            }

            $ProductListingPayment = ProductListingPayment::where('seller_id', $order->sellers_id)
            ->where('payment_status', 'paid')
            ->latest()
            ->first();

            if ($ProductListingPayment) {
                
                $ProductListingPayment->update([
                    'payment_status' => 'unpaid'
                ]);
            }



            Notification::create([
                'customer_id' => $user->id,
                'notification' => 'your order #'.$order->id.'has been stoped to deliver due to some miss understanding!'
            ]);


        }

        $order->delivery_status = $request->delivery_status;
        $order->save();

        OrderTimeline::create([
            'seller_id' => $seller->id,
            'customer_id' => $order->customer_id,
            'order_id' => $request->id,
            'time_line' => 'Order cancelled'
        ]);

        $response = ['status'=>true,"message" => "Status Changed Successfully!",'TrackingNumber'=>$TrackingNumber];
        return response($response, 200);
    }

    public function tracking_update(Request $request)
    {
        OrderTracking::where('order_id',$request->order_id)->update([
            'tracking_number' => $request->tracking_number,
            'courier_name' => $request->courier_name,
            'courier_link' => $request->courier_link
        ]);

        $response = ['status'=>true,"message" => "updated successfully!"];
        return response($response, 200);
    }

    public function update_tags(Request $request)
    {
        Order::where('id', $request->id)->update(['tags' => $request->tags]);
        $response = ['status'=>true,"message" => "tag saved successfully!"];
        return response($response, 200);
    }

    public function payment_status(Request $request)
    {
        $changeStatus = Order::where('id',$request->id)->first();
        $changeStatus->payment_status = $request->payment_status;
        $changeStatus->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        Order::find($id)->delete();

        $response = ['status'=>true,"message" => "Order Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = Order::whereIn('id',$request->ids)->delete();

        $response = ['status'=>true,"message" => "Orders Deleted Successfully!"];
        return response($response, 200);
    }
}
