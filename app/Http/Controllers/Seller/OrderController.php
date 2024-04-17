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
use Mail;

class OrderController extends Controller
{
    public function index($id)
    {
        $data = Order::with('order_detail.products.product_gallery','order_detail.products.category','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.products.brand','order_detail.products.model','order_detail.products.stock','order_detail.varient','order_detail.products.reviews.user','order_detail.products.tax','order_detail.products.shop.shop_policy','order_status','order_tracking','order_refund','shop','nagative_payout_balance')->where('sellers_id',$id)->get();

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

            $OrderStatus = new OrderStatus();
            $OrderStatus->order_id = $request->id;
            $OrderStatus->status = 'deliverd';
            $OrderStatus->save();

            $TrackingOrder = new OrderTracking();
            $TrackingOrder->order_id = $order->id;
            $TrackingOrder->tracking_number = $request->tracking_number;
            $TrackingOrder->courier_name = $request->courier_name;
            $TrackingOrder->courier_link = $request->courier_link;
            $TrackingOrder->shipping_label = $request->shipping_label;

            $TrackingOrder->save();



            Mail::send(
                'email.Order.order_completed',
                [
                    'buyer_name' => $user->name,
                    'shop' => $shop,
                    'order'=> $order,
                    'TrackingOrder' => $TrackingOrder,
                    'date' => Carbon::today()->toDateString()
                ],
                function ($message) use ($user) { // Add $user variable here
                    $message->from('support@dragonautomart.com','Dragon Auto Mart');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );


            
            $NewPayout = new Payout();
            $NewPayout->date = Carbon::now();
            $NewPayout->seller_id = $order->sellers_id;
            $NewPayout->order_id = $order->id;
            
            
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
            
            $featuredAmount = FeaturedProductOrder::where('order_id', $order->id)->where('payment_status','unpaid')->sum('payment') ?? 0;
            $nagativePayoutBalance = NagativePayoutBalance::where('seller_id', $order->sellers_id)
            ->where('payment_status','unpaid')
            ->first();

            $nagativePayoutAmount = $nagativePayoutBalance ? $nagativePayoutBalance->amount : 0;


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
            $NewPayout->amount = floatval($adjustedAmountInDollars) - floatval($featuredAmount) - floatval($ListingPayment) - floatval($order->shipping_amount) - floatval($nagativePayoutAmount);
            $NewPayout->save();

            if($NewPayout->amount < 0)
            {
                $NagativePayoutBalance = new NagativePayoutBalance();
                $NagativePayoutBalance->seller_id = $order->sellers_id;
                $NagativePayoutBalance->amount = $NewPayout->amount;
                $NagativePayoutBalance->save();
            }


            if($featuredAmount > 0)
            {
                FeaturedProductOrder::where('order_id', $order->id)->update(['payment_status'=>'paid']);

            }

            if($nagativePayoutBalance > 0)
            {
                NagativePayoutBalance::where('order_id', $order->id)->update(['payment_status'=>'paid']);

            }
            
            

            $notification = new Notification();
            $notification->customer_id = $user->id;
            $notification->notification = 'your order #'.$order->id.'has been ready to delivered';
            $notification->save();


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


            $NagativePayoutBalance = NagativePayoutBalance::where('order_id', $order->id)
            ->where('payment_status', 'paid')
            ->latest()
            ->first();

            if ($NagativePayoutBalance) {
                
                $NagativePayoutBalance->update([
                    'payment_status' => 'unpaid'
                ]);
            }




            

            $notification = new Notification();
            $notification->customer_id = $user->id;
            $notification->notification = 'your order #'.$order->id.'has been stoped to deliver due to some miss understanding!';
            $notification->save();

        }

        $order->delivery_status = $request->delivery_status;
        $order->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!",'TrackingNumber'=>$TrackingNumber];
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
