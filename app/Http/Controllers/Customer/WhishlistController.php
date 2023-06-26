<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Whishlist;

class WhishlistController extends Controller
{
    public function index($id)
    {
        $data = Whishlist::where('customer_id',$id)->get();

        return response()->json(["data"=>$data]);
    }

    public function create(Request $request)
    {
        $check = Whishlist::where('customer_id',$request->customer_id)->where('product_id',$request->product_id)->first();

        if($check)
        {
            $response = ['status'=>true,"message" => "This Product is already in your wishlist!"];
            return response($response, 200);
        }
        else
        {
            $new = new Whishlist();
            $new->customer_id = $request->customer_id;
            $new->product_id = $request->product_id;
            $new->save();

            $response = ['status'=>true,"message" => "Product added in whishlist Successfully!!"];
            return response($response, 200);
        }
    }

    public function delete($id)
    {
        Whishlist::find($id)->delete();

        $response = ['status'=>true,"message" => "Deleted Successfully!!"];
        return response($response, 200);
    }
}
