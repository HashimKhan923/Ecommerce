<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ModelProductController extends Controller
{
    public function index($model_id)
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
        ])->where('published',1)->orderBy('id', 'desc')->where('model_id',$model_id)->take(24)->get();

        return response()->json(['data'=>$data]);
    }


    public function load_more($model_id, $length)
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
        ])->where('published',1)->orderBy('id', 'desc')->where('model_id',$model_id)->skip($length)->take(12)->get();

        return response()->json(['data'=>$data]);
    }
}
