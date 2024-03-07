<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;

class ShopController extends Controller
{
    public function index()
    {
        $data = Shop::with('shop_policy')->withCount('product')->get();

        return response()->json(['data'=>$data]);
    }
}
