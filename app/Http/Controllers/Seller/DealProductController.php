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
        foreach ($request->deal_product as $deal_product) {
            DealProduct::create(
                [
                    'deal_id' => 4,
                    'product_id' => $deal_product['product_id'],
                ],
            );
        }

    }
}
