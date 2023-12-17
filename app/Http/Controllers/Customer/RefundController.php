<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Order;

class RefundController extends Controller
{
    public function index($customer_id)
    {
        $data = Refund::with('order')->where('customer_id',$customer_id)->get();

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

        $new = new Refund();
        $new->order_id = $request->order_id;
        $new->seller_id = $request->seller_id;
        $new->reason = $request->reason;
        $new->save();

        $response = ['status'=>true,'message'=>'Refund request sent successfully!'];
        return response($response,200);

    }

    public function delete($id)
    {
        Refund::find($id)->delete();

        $response = ['status'=>true,'message'=>'deleted successfully!'];
        return response($response,200);
    }

}
