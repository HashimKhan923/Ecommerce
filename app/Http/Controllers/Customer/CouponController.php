<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $check = Coupon::where('code',$request->code)
        ->where('end_date','>',Carbon::now())
        ->first();

        if($check)
        {
            $response = ['status'=>true,"message" => "Coupon matched Successfully!","data" => $check];
            return response($response, 200);
        }
        else
        {
            $response = ['status'=>false,"message" => "invalid coupon!"];
            return response($response, 422);
        }
    }
}
