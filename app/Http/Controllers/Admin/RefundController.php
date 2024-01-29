<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;
use App\Models\User;
use App\Models\Payout;
use App\Models\Notification;
use Mail;
use Stripe\Stripe;
use Stripe\Refund as StripeRefund;
use PayPal\Api\Refund as PaypalRefund;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Sale;



class RefundController extends Controller
{
    public function index()
    {
        $data = Refund::with('order')->get();

        return response()->json(['data'=>$data]);
    }

    public function approved_refunds()
    {
        $data = Refund::where('refund_status','Approved')->get();

        return response()->json(['data'=>$data]);
    }

    public function rejected_refunds()
    {
        $data = Refund::where('refund_status','Rejected')->get();

        return response()->json(['data'=>$data]);
    }

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

        $notification = new Notification();
        $notification->customer_id = $user->id;
        $notification->notification = 'your #'.$Order->id.' refund request has been fulfield by admin and you will recive your amount in 5 to 10 working days.';
        $notification->save();


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

        $notification = new Notification();
        $notification->customer_id = $user->id;
        $notification->notification = 'your #'.$order->id.' refund request has been fulfield by admin and you will recive your amount in 5 to 10 working days.';
        $notification->save();


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
