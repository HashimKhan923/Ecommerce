<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Notification;
use App\Models\User;
use App\Models\Payout;
use Mail;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Refund as PaypalRefund;
use PayPal\Api\Sale;
use PayPal\Exception\PayPalException;
use PayPal\Api\Amount;
use App\Models\OrderTimeline;
class RefundController extends Controller
{
    public function index($seller_id)
    {
        $data = Refund::with('order.order_detail.products.shop')->where('seller_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {

        $check = Order::where('id',$request->order_id)->first();
        if($check->delivery_status == 'Delivered')
        {
            $response = ['status'=>true,'message'=>'your order has been delivered'];
            return response($response,201);
        }

        $checkRefund = Refund::where('order_id',$request->order_id)->first();
        if($checkRefund)
        {
            $response = ['status'=>true,'message'=>'you have already requested for this refund'];
            return response($response,201);
        }

        
        $Order = Order::with('order_detail.products.product_gallery')->where('id',$request->order_id)->first();
        $Shop = Shop::where('id',$request->shop_id)->first();
        $Customer = User::where('id',$request->customer_id)->first();

        $new = new Refund();
        $new->order_id = $request->order_id;
        $new->seller_id = $request->seller_id;
        $new->shop_id = $request->shop_id;
        $new->reason = $request->reason;
        $new->seller_approval = 'Approved';
        $new->save();

        
        Mail::send(
            'email.Order.order_cancelled',
            [
                'buyer_name' => $Customer->name,
                'shop' => $Shop,
                'order'=> $Order,
                'body'=> $request->reason
            ],
            function ($message) use ($Customer) { 
                $message->from('support@dragonautomart.com','Dragon Auto Mart');
                $message->to($Customer->email);
                $message->subject('Order Cancelled');
            }
        );

        $response = ['status'=>true,'message'=>'Refund request sent successfully!','refund_id'=>$new->id];
        return response($response,200);

    }

    // public function is_approved($refund_id)
    // {
    //     $is_approved = Refund::where('id',$refund_id)->first();
    //     $order = Order::where('id',$is_approved->order_id)->first();

    //     $notification = new Notification();
    //     $notification->customer_id = $order->customer_id;

    //     if($is_approved->seller_approval == 'Not Approved')
    //     {
    //         $is_approved->seller_approval = 'Approved';

    //         $notification->notification = 'your order #'.$order->id.' refund has been approved by seller';
            
    //     }
    //     else
    //     {
    //         $is_approved->seller_approval = 'Not Approved';
    //         $notification->notification = 'your order #'.$order->id.' refund not approved by seller';

    //     }

    //     $notification->save();
    //     $is_approved->save();
    // }

    public function stripe_refund(Request $request)
    {

        Stripe::setApiKey(config('services.stripe.secret'));


        $refund = StripeRefund::create([
            'payment_intent' => $request->payment_intent_id,
            'amount' => $request->amount * 100,
        ]);


        $change = Refund::where('id',$request->id)->first();
        $change->refund_status = $request->refund_status;
        $change->save();


        Payout::where('order_id',$change->order_id)->delete();

        $Order = Order::with('order_detail.products.product_single_gallery')->where('id',$change->order_id)->first();
        $user = User::where('id',$Order->customer_id)->first();

        Notification::create([
            'customer_id' => $user->id,
            'notification' => 'your order #'.$Order->id.' refund request has been fulfilled and you will receive your amount in 5 to 10 working days.',
        ]);

        Notification::create([
            'customer_id' => $Order->sellers_id,
            'notification' => 'your order #'.$Order->id.' refund request has been fulfilled.'
        ]);
        


        Mail::send(
            'email.Order.order_refund',
            [
                'buyer_name' => $user->name,
                'order' => $Order,
                // 'last_name' => $query->last_name
            ],
            function ($message) use ($user) {
                $message->from('support@dragonautomart.com','Dragon Auto Mart');
                $message->to($user->email);
                $message->subject('Order Refund');
            }
        );

        OrderTimeline::create([
            'order_id' => $change->order_id,
            'time_line' => 'The amount ({{$request->amount}}) has been refunded via Stripe'
        ]);

        $response = ['status'=>true,"message" => "Refund processed successfully!"];
        return response($response, 200);
    }


    public function paypal_refund(Request $request)
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.secret')
            )
        );
        $apiContext->setConfig([
            'mode' => config('services.paypal.mode'),
        ]);
    
        try {
            $saleId = $request->payment_intent_id; // You need to get the sale ID from your previous transaction
    
            // Create a new refund amount object
            $amount = new Amount();
            $amount->setTotal($request->amount);
            $amount->setCurrency('USD'); // Set the currency according to your transaction
    
            // Create a new refund object
            $refund = new PaypalRefund();
            $refund->setAmount($amount);
    
            // Get the sale object by sale ID
            $sale = new Sale();
            $sale->setId($saleId);
    
            // Perform the refund
            $refundResult = $sale->refund($refund, $apiContext);
    
            // Update the refund status in the database
            $change = Refund::where('id', $request->id)->first();
            $change->refund_status = $request->refund_status;
            $change->save();
    
            // Delete related payout
            Payout::where('order_id', $change->order_id)->delete();
    
            // Get the order details
            $Order = Order::with('order_detail.products.product_single_gallery')->where('id', $change->order_id)->first();
            $user = User::where('id', $Order->customer_id)->first();
    
            // Create notifications
            Notification::create([
                'customer_id' => $user->id,
                'notification' => 'Your order #' . $Order->id . ' refund request has been fulfilled and you will receive your amount in 5 to 10 working days.',
            ]);
    
            Notification::create([
                'customer_id' => $Order->sellers_id,
                'notification' => 'Order #' . $Order->id . ' refund request has been fulfilled.'
            ]);
    
            // Send email notification
            Mail::send(
                'email.Order.order_refund',
                [
                    'buyer_name' => $user->name,
                    'order' => $Order,
                ],
                function ($message) use ($user) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to($user->email);
                    $message->subject('Order Refund');
                }
            );

            OrderTimeline::create([
                'order_id' => $change->order_id,
                'time_line' => 'The amount ({{$request->amount}}) has been refunded via Paypal'
            ]);
    
            return response()->json(['success' => true, 'message' => 'Refund processed successfully']);
    
        } catch (\Exception $e) {
            // Handle refund failure
            return response()->json(['success' => false, 'message' => 'Refund failed', 'error' => $e->getMessage()], 500);
        }
    }
}
