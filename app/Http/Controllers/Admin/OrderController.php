<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;
use Mail;
class OrderController extends Controller
{


    public function index()
    {
        $data = Order::with('order_detail')->get();

        return response()->json(['data'=>$data]);
    }

    public function admin_orders($id)
    {
        $data = Order::with('order_detail')->where('seller_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function seller_orders($id)
    {
        $data = Order::with('order_detail')->where('seller_id','!=',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function delivery_status(Request $request)
    {
        $order = Order::where('id',$request->id)->first();

        $user = User::where('id',$order->customer_id)->first();

        if($request->delivery_status == 'Confirmed')
        {
            Mail::send(
                'email.Order.order_confirmation',
                [
                    'buyer_name' => $user->name,
                    // 'last_name' => $query->last_name
                ],
                function ($message) use ($user) { // Add $user variable here
                    $message->from('support@dragonautomart.com');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );
        }
        elseif($request->delivery_status == 'Delivered')
        {

            Mail::send(
                'email.Order.order_completed',
                [
                    'buyer_name' => $user->name,
                    // 'last_name' => $query->last_name
                ],
                function ($message) use ($user) { // Add $user variable here
                    $message->from('support@dragonautomart.com');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );

            $NewPayout = new Payout();
            $NewPayout->date = Carbon::now();
            $NewPayout->seller_id = $order->seller_id;
            $NewPayout->order_id = $order->id;
            $NewPayout->amount = $order->amount;
            $NewPayout->payment_status = $order->payment_method;
            $NewPayout->save();

        }
        else
        {

            Mail::send(
                'email.Order.order_ontheway',
                [
                    'buyer_name' => $user->name,
                    // 'last_name' => $query->last_name
                ],
                function ($message) use ($user) { // Add $user variable here
                    $message->from('support@dragonautomart.com');
                    $message->to($user->email);
                    $message->subject('Order Confirmation');
                }
            );


        }

        $order->delivery_status = $request->delivery_status;
        $order->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
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
