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

        $response = ['status'=>true,'message'=>'Refund request sent successfully!'];
        return response($response,200);

    }

    public function is_approved($refund_id)
    {
        $is_approved = Refund::where('id',$refund_id)->first();
        $order = Order::where('id',$is_approved->order_id)->first();

        $notification = new Notification();
        $notification->customer_id = $order->customer_id;

        if($is_approved->seller_approval == 'Not Approved')
        {
            $is_approved->seller_approval = 'Approved';

            $notification->notification = 'your order #'.$order->id.' refund has been approved by seller';
            
        }
        else
        {
            $is_approved->seller_approval = 'Not Approved';
            $notification->notification = 'your order #'.$order->id.' refund not approved by seller';

        }

        $notification->save();
        $is_approved->save();
    }
}
