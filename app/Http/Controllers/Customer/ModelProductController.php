<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ModelProductController extends Controller
{
    private function getProductsByModel($model_id, $length = null)
    {
        $query = Product::with([
            'user', 'category', 'brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping', 'deal.deal_product',
            'wholesale', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        ->where('model_id', $model_id)
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
    
    public function index($model_id)
    {
        $data = $this->getProductsByModel($model_id);
        return response()->json(['data' => $data]);
    }
    
    public function load_more($model_id, $length)
    {
        $data = $this->getProductsByModel($model_id, $length);
        return response()->json(['data' => $data]);
    }
    
}
