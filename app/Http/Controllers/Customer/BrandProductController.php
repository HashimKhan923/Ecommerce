<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class BrandProductController extends Controller
{
    private function getProductsByBrand($brand_id, $length = null)
    {
        $query = Product::with([
            'user', 'category', 'brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping', 'deal.deal_product',
            'wholesale', 'shop', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        ->where('brand_id', $brand_id)
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
    
    public function index($brand_id)
    {
        $data = $this->getProductsByBrand($brand_id);
        return response()->json(['data' => $data]);
    }
    
    public function load_more($brand_id, $length)
    {
        $data = $this->getProductsByBrand($brand_id, $length);
        return response()->json(['data' => $data]);
    }
    
}
