<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealProduct;
class DealProductController extends Controller
{
    public function create(Request $request)
    {
        foreach ($request->deal_product as $deal_product) {
            DealProduct::create(
                [
                    'deal_id' => $deal_product['deal_id'],
                    'product_id' => $deal_product['product_id'],
                ],
            );
        }

    }
}
