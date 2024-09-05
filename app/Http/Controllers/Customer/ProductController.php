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
    // public function index()
    // {
    //     $Products = Product::with('user','category','brand','stock','product_gallery','discount','tax','shipping','deal','wholesale','shop','reviews','product_varient')->where('published',1)->where('approved',1)->get();
    //     $Categories = Category::where('is_active',1)->get();
    //     $Barnds = Brand::where('is_active',1)->get();


    //     return response()->json(['Products'=>$Products,'Categories'=>$Categories,'Brands'=>$Brands]);

    // }

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
            'deal.deal_product',
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
