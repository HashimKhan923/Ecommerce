<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class SellerController extends Controller
{
    public function index()
    {
        $Sellers = User::with('shop','seller_information','SellingPlatforms','SocialPlatforms','BankDetail','CreditCard')->where('user_type','seller')->get();

        return response()->json(["Sellers"=>$Sellers]);
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

    public function is_verify($id)
    {
        $is_active = User::where('id',$id)->first();
        $is_active->is_verify = 1;
        $is_active->save();

        Mail::send(
            'email.seller_account_verification',
            [
                'name'=>$is_active->name,
            ], 
        
        function ($message) use ($is_active) {
            $message->from('support@dragonautomart.com','Dragon Auto Mart');
            $message->to($is_active->email);
            $message->subject('Account Verification');
        });

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

 

        

        $response = ['status'=>true,"message" => "Sellers Deleted Successfully!"];
        return response($response, 200);
    }
}
