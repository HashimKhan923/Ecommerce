<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVarient;

class VarientController extends Controller
{
    public function delete($id)
    {
        ProductVarient::where('id',$request->id)->where('product_id',$request->product_id)->delete();

        $response = ['status'=>true,"message" => "Varient Deleted Successfully!"];
        return response($response, 200);
    }
}
