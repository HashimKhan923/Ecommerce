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

    public function change_status(Request $request)
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

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }
}
