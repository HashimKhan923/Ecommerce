<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;
use App\Models\Notification;

class RefundController extends Controller
{
    public function index($seller_id)
    {
        $data = Refund::with('order.order_detail.products.shop')->where('seller_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
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
