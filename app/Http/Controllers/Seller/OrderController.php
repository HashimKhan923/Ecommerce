<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payout;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index($id)
    {
        $data = Order::with('order_detail')->where('seller_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function delivery_status(Request $request)
    {
        $order = Order::where('id',$request->id)->first();

        if($request->delivery_status == 'Confirmed')
        {
            Mail::send(
                'email.order_confirmation',
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
                'email.order_completed',
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
        else
        {

            Mail::send(
                'email.order_ontheway',
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
            $NewPayout->payment_status = $order->payment_status;
            $NewPayout->save();

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
}
