<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealProduct;
class DealProductController extends Controller
{

    public function show($seller_id)
    {
        $dealProduct = DealProduct::with(['product' => function($query) use ($seller_id) {
            $query->where('user_id', $seller_id);
        }])->first();

        return response()->json(['data'=>$dealProduct]);
    }

    public function create(Request $request)
    {
        foreach ($request->deal_product as $product_id) {
            DealProduct::create([
                'deal_id' => 4,  
                'product_id' => $product_id,  
            ]);
        }

        $response = ['status'=>true,"message" => "Products Added Successfully!"];
        return response($response, 200);

    }


    public function multi_delete(Request $request)
    {
        Product::whereIn('id',$request->ids)->delete();

        $response = ['status'=>true,"message" => "Products Deleted Successfully!"];
        return response($response, 200);
    }
}