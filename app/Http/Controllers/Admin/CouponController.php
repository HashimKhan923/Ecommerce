<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index()
    {
        $data = Coupon::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Coupon();
        $new->name = $request->name;
        $new->code = $request->code;
        $new->discount = $request->discount;
        $new->discount_type = $request->discount_type;
        $new->start_date = $request->start_date;
        $new->end_date = $request->end_date;
        $new->save();

        $response = ['status'=>true,"message" => "Coupon Created Successfully!"];
        return response($response, 200);
    }

    public function update(Request $request)
    {
        $update = Coupon::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->code = $request->code;
        $update->discount = $request->discount;
        $update->discount_type = $request->discount_type;
        $update->start_date = $request->start_date;
        $update->end_date = $request->end_date;
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
}
