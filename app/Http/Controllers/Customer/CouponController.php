<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\CouponUser;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $check = Coupon::with('creator','shop','coupon_customers.customer','coupon_categories.category','coupon_products.product.product_gallery')->where('code',$request->code)
        ->where('end_date','>',Carbon::now())
        ->first();

        $check_user = CouponUser::where('coupon_id',$check->id)->first();

        if($check)
        {
            if(!$check_user)
            {
                $response = ['status'=>true,"message" => "Coupon matched Successfully!","data" => $check];
                return response($response, 200);
            }
            else
            {
                $response = ['status'=>false,"message" => "you have already used this coupon!"];
                return response($response, 422);
            }

        }
        else
        {
            $response = ['status'=>false,"message" => "invalid coupon!"];
            return response($response, 422);
        }
    }
}
