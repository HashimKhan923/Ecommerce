<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    public function index()
    {
        $Customers = User::where('user_type','customer')->get();

        return response()->json(["Customers"=>$Customers]);
    }

    public function is_active($id)
    {
        $is_active = User::where('id',$id)->first();

        if($is_active->is_active == 0)
        {
            $is_active = 1;
        }
        else
        {
            $is_active = 0;
        }

        $is_active->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }
}
