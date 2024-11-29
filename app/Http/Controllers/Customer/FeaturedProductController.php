<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;


class FeaturedProductController extends Controller
{
    public function index()
    {
        $FeaturedProducts = Product::with([
            'user','wishlistProduct', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping',
            'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        // ->whereHas('stock', function ($query) {
        //     $query->where('stock', '>', 0);
        // })
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->where('featured',1)
        ->take(24)->get();

        return response()->json(['FeaturedProducts'=>$FeaturedProducts]);
    }

    public function load_more($length)
    {
        $FeaturedProducts = Product::with([
            'user','wishlistProduct', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping',
            'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        // ->whereHas('stock', function ($query) {
        //     $query->where('stock', '>', 0);
        // })
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->where('featured',1)
        ->skip($length)
        ->take(24)->get();

        return response()->json(['FeaturedProducts'=>$FeaturedProducts]);

    }
}
