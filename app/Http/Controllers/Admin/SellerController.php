<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SellerController extends Controller
{
    public function index()
    {
        $Sellers = User::where('user_type','seller')->get();

        return response()->json(["Sellers"=>$Sellers]);
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
