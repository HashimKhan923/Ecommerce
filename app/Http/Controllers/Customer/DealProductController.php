<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class DealProductController extends Controller
{
    public function index()
    {
        $DealProducts = Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount','deal','tax', 'shipping',
            'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->where('deal_id',4)
        ->take(24)->get();

        return response()->json(['DealProducts'=>$DealProducts]);
    }

    public function load_more($length)
    {
        $DealProducts = Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount','deal','tax', 'shipping',
            'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->where('deal_id',4)
        ->skip($length)
        ->take(24)->get();

        return response()->json(['DealProducts'=>$DealProducts]);
    }
}
