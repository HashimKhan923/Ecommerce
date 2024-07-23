<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Notification;
use App\Models\User;
use Mail;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Refund as PaypalRefund;
use PayPal\Api\Sale;
use PayPal\Exception\PayPalException;

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

        $new = new Refund();
        $new->order_id = $request->order_id;
        $new->seller_id = $request->seller_id;
        $new->shop_id = $request->shop_id;
        $new->reason = $request->reason;
        $new->seller_approval = 'Approved';
        $new->save();

        $Order = Order::with('order_detail.products.product_gallery')->where('id',$request->order_id)->first();
        $Shop = Shop::where('id',$request->shop_id)->first();
        $Customer = User::where('id',$request->customer_id)->first();
        
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
            'notification' => 'your order #'.$Order->id.' refund request has been fulfilled by admin and you will recive your amount in 5 to 10 working days.',
        ]);

        Notification::create([
            'customer_id' => $Order->sellers_id,
            'notification' => 'your order #'.$Order->id.' refund request has been fulfilled by admin.'
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
        
            try {
                $saleId = $request->payment_intent_id; // You need to get the sale ID from your previous transaction
        
                $refund = new PaypalRefund();
                $refund->setAmount([
                    'total' => $request->amount,
                    'currency' => 'USD', // Set the currency according to your transaction
                ]);
        
                $refund->setSaleId($saleId);
        
                // Perform the refund
                $refundResult = $refund->refund($apiContext);



        $change = Refund::where('id',$request->id)->first();
        $change->refund_status = $request->refund_status;
        $change->save();


        Payout::where('order_id',$change->order_id)->delete();

        $Order = Order::with('order_detail.products.product_single_gallery')->where('id',$change->order_id)->first();
        $user = User::where('id',$Order->customer_id)->first();

        Notification::create([
            'customer_id' => $user->id,
            'notification' => 'your #'.$Order->id.' refund request has been fulfield by admin and you will recive your amount in 5 to 10 working days.',
        ]);

        Notification::create([
            'customer_id' => $Order->sellers_id,
            'notification' => '#'.$Order->id.' refund request has been fulfield by admin.'
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

            return response()->json(['success' => true, 'message' => 'Refund processed successfully']);

        } catch (\Exception $e) {
            // Handle refund failure
            return response()->json(['success' => false, 'message' => 'refund failed', 'error' => $e->getMessage()], 500);
        }
    }
}
