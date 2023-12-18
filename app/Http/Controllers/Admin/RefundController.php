<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;
use App\Models\User;
use Mail;

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
        $change = Refund::where('id',$request->id)->first();
        $change->refund_status = $request->refund_status;
        $change->save();


        $Order = Order::with('order_detail.products.product_single_gallery')->where('id',$change->order_id)->first();
        $user = User::where('id',$Order->customer_id)->first();


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
