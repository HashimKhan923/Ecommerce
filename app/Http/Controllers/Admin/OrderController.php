<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
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
        $changeStatus = Order::where('id',$request->id)->first();
        $changeStatus->delivery_status = $request->delivery_status;
        $changeStatus->save();

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
