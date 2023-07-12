<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ReportController extends Controller
{
    public function admin_product_sale(Request $request)
    {
        $data = Product::where('added_by','admin')->where('category_id',$request->category_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function saller_product_sale(Request $request)
    {
        $data = Product::where('added_by','seller')->where('category_id',$request->category_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function product_stock(Request $request)
    {
        $data = Product::with('stock')->where('category_id',$request->category_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function product_wishlist(Request $request)
    {
        $data = Product::with('wishlist')->where('category_id',$request->category_id)->get();

        return response()->json(['data'=>$data]);
    }
}
