<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $Customers = User::with('order.order_timeline')->where('user_type','customer')->get();

        return response()->json(["Customers"=>$Customers]);
    }

    public function is_active($id)
    {
        $is_active = User::where('id',$id)->first();

        if($is_active->is_active == 0)
        {
            $is_active->is_active = 1;
        }
        else
        {
            $is_active->is_active = 0;
        }

        $is_active->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        User::find($id)->delete();

        $response = ['status'=>true,"message" => "Customer Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        User::whereIn('id',$request->ids)->delete();

        $response = ['status'=>true,"message" => "Users Deleted Successfully!"];
        return response($response, 200);
    }
}
