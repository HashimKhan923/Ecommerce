<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockNotifyMe;

class StockNotifyMeController extends Controller
{
    public function create(Request $request)
    {
        StockNotifyMe::create([
            'email'=>$request->email,
            'product_id'=>$request->product_id,
            'variant_id'=>$request->variant_id
        ]);

        return response()->json(['message'=>'Thank you! You will be notified when the product is back in stock.']);
    }
}
