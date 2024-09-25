<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Product;
class DealProductController extends Controller
{

    // public function index($seller_id)
    // {
    //     $dealProduct = Product::with('product_single_gallery','shop')->where('user_id', $seller_id)->where('deal_id',4)->get();
    
    //     return response()->json(['data' => $dealProduct]);
    // }

    public function create(Request $request)
    {
        foreach ($request->deal_product as $product_id) {
            Product::where('id', $product_id)->update([
                'deal_id' => 4,
            ]);
        }

        $response = ['status'=>true,"message" => "Products Added Successfully!"];
        return response($response, 200);

    }


    public function multi_delete(Request $request)
    {
        Product::whereIn('id',$request->ids)->update([
            'deal_id'=>null
        ]);

        $response = ['status'=>true,"message" => "Products Deleted Successfully!"];
        return response($response, 200);
    }
}
