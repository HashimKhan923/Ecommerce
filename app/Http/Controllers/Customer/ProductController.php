<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\ProductComments;
use App\Models\ProductRating;

class ProductController extends Controller
{
    public function index()
    {
        $Products = Product::with([
            'user', 'category', 'sub_category', 'brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping', 'deal', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)
          ->whereHas('stock', function ($query) {
              $query->where('stock', '>', 0);
          })
          ->whereHas('shop', function ($query) {
              $query->where('status', 1);
          })
          ->orderByRaw('featured DESC') // Prioritize featured first
        //   ->orderBy('id', 'desc') // Then by id in descending order
          ->take(24)
          ->get();
        


        return response()->json(['Products'=>$Products]);

    }

    private function loadMoreProducts($orderBy, $length)
    {
        return Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping','deal','shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)->orderBy($orderBy, 'desc')->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })
        ->orderByRaw('featured DESC') 
        // ->orderBy('id', 'desc')
        ->skip($length)->take(24)->get();
    }
    
    public function load_more($length)
    {
        $Products = $this->loadMoreProducts('id', $length);
        return response()->json(['Products' => $Products]);
    }

    public function detail($id)
    {
        $data = Product::with([
            'user',
            'category',
            'sub_category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal',
            'wholesale',
            'shop.shop_policy',
            'reviews.user',
            'product_varient'
        ])->where('id',$id)->first();


        return response()->json(['data'=>$data]);



    }

    public function comment(Request $request)
    {
        $new = new ProductComments();
        $new->product_id = $request->product_id;
        $new->person_name = $request->person_name;
        $new->comment = $request->comment;
        $new->save();
    }

    public function rating(Request $request)
    {
        $new = new ProductRating();
        $new->product_id = $request->product_id;
        $new->user_id = $request->user_id;
        $new->rating = $request->rating;
        $new->save();
    }
}
