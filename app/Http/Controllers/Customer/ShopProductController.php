<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ShopProductController extends Controller
{
    public function index($shop_id)
    {
        $data = Product::with([
            'user',
            'category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal.deal_product',
            'wholesale',
            'shop.shop_policy',
            'reviews.user',
            'product_varient'
        ])->where('published',1)->orderBy('id', 'desc')->where('shop_id',$shop_id)->take(24)->get();

        return response()->json(['data'=>$data]);
    }


    public function load_more($shop_id, $length)
    {
        $data = Product::with([
            'user',
            'category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal.deal_product',
            'wholesale',
            'shop',
            'reviews.user',
            'product_varient'
        ])->where('published',1)->orderBy('id', 'desc')->where('shop_id',$shop_id)->skip($length)->take(12)->get();

        return response()->json(['data'=>$data]);
    }
}
