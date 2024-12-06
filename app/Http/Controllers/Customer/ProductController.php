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
    // Common method to fetch products
    private function fetchProducts($length = null, $limit = 24, $searchValue = null)
    {

        
        $query = Product::with([
            'user', 'wishlistProduct', 'category', 'sub_category', 'brand', 'model', 'stock',
            'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'discount', 'tax', 'shipping', 'deal', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])
        ->where('published', 1)
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })
        ->orderByRaw('featured DESC, id DESC'); // Prioritize featured products and sort by ID
    
    
        // Apply search filter if provided
        if (!empty($searchValue)) {
            $keywords = explode(' ', $searchValue); // Split the searchValue into keywords
            $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('sku', 'LIKE', "%{$keyword}%")
                            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereJsonContains('tags', $keyword); // Assuming 'tags' is stored as JSON
                    });
                }
            });
        }
    
        // Apply pagination
        // if (!is_null($length)) {
        //     $query->skip($length)->take($limit);
        // } else {
        //     $query->take($limit);
        // }
    
        return $query->get();
    }

    // Index method to load initial products
    public function index()
    {
        // Fetch the first 24 products
        $Products = $this->getProducts();

        return response()->json(['Products' => $Products]);
    }

    public function load_more($length, $searchValue = null)
    {
        $products = $this->fetchProducts($length, $searchValue);
        return response()->json(['Products' => $products]);
    }


    public function detail($id)
    {
        $data = Product::with([
            'user','wishlistProduct',
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
