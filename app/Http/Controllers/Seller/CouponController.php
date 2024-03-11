<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index($seller_id)
    {
        $data = Coupon::where('creator_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Coupon();
        $new->creator_id = $request->creator_id;
        $new->shop_id = $request->shop_id;
        $new->product_id = $request->product_id;
        $new->customer_id = $request->customer_id;
        $new->category_id = $request->category_id;
        $new->brand_id = $request->brand_id;
        $new->model_id = $request->model_id;
        $new->minimum_purchase_amount = $request->minimum_purchase_amount;
        $new->name = $request->name;
        $new->code = $request->code;
        $new->discount = $request->discount;
        $new->discount_type = $request->discount_type;
        $new->start_date = Carbon::parse($request->start_date);
        $new->end_date = Carbon::parse($request->end_date);
        $new->save();

        $response = ['status'=>true,"message" => "Coupon Created Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = Coupon::where('id',$request->id)->first();
        $update->shop_id = $request->shop_id;
        $update->product_id = $request->product_id;
        $update->customer_id = $request->customer_id;
        $update->category_id = $request->category_id;
        $update->brand_id = $request->brand_id;
        $update->model_id = $request->model_id;
        $update->minimum_purchase_amount = $request->minimum_purchase_amount;
        $update->name = $request->name;
        $update->code = $request->code;
        $update->discount = $request->discount;
        $update->discount_type = $request->discount_type;
        $update->start_date = Carbon::parse($request->start_date);
        $update->end_date = Carbon::parse($request->end_date);
        $update->save();

        $response = ['status'=>true,"message" => "Coupon Updated Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        Coupon::find($id)->delete();

        $response = ['status'=>true,"message" => "Coupon Deleted Successfully!"];
        return response($response, 200);
    }

    public function status($id)
    {
        $status = Coupon::find($id);

        if($status->status == 1)
        {
            $status->status = 0;
        }
        else
        {
            $status->status = 1;
        }
        $status->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }
}
