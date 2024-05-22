<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CategoryProductController extends Controller
{
    private function getProductsWithRelationships($category_id, $length = null)
    {
        $query = Product::with([
            'user', 'category', 'brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping', 'deal.deal_product',
            'wholesale', 'shop', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        ->where('category_id', $category_id)
        ->orderBy('id', 'desc')->whereHas('stock', function ($query) {
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
    
    public function index($category_id)
    {
        $data = $this->getProductsWithRelationships($category_id);
        return response()->json(['data' => $data]);
    }
    
    public function load_more($category_id, $length)
    {
        $data = $this->getProductsWithRelationships($category_id, $length);
        return response()->json(['data' => $data]);
    }
}
