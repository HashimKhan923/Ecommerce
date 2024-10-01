<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class SubCategoryProductController extends Controller
{
    private function getProductsWithRelationships($sub_category_id, $length = null)
    {
        $query = Product::with([
            'user', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping', 'deal',
            'wholesale', 'shop', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        ->where('sub_category_id', $sub_category_id)
        ->orderByRaw('featured DESC')->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->whereHas('shop', function ($query) {
            $query->where('status', 1);
        });
    
        if ($length !== null) {
            $query->skip($length)->take(12);
        } else {
            $query->take(24);
        }
    
        return $query->get();
    }
    
    public function index($sub_category_id)
    {
        $data = $this->getProductsWithRelationships($sub_category_id);
        return response()->json(['data' => $data]);
    }
    
    public function load_more($sub_category_id, $length)
    {
        $data = $this->getProductsWithRelationships($sub_category_id, $length);
        return response()->json(['data' => $data]);
    }
}
