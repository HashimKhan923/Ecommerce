<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVarient;

class VarientController extends Controller
{
    public function delete(Request $request)
    {
        ProductVarient::where('id',$request->id)->where('product_id',$request->product_id)->delete();

        $response = ['status'=>true,"message" => "Varient Deleted Successfully!"];
        return response($response, 200);
    }
}