<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ShopProductController extends Controller
{
    private function getProductRelations()
    {
        return [
            'user',
            'category',
            'sub_category',
            'brand',
            'model',
            'stock' => function($query) {
                $query->where('stock', '>', 0);
            },
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal.deal_product',
            'wholesale',
            'shop' => function($query) {
                $query->where('status', 1);
            },
            'shop.shop_policy',
            'reviews.user',
            'product_varient'
        ];
    }

    private function formatResponse($data)
    {
        return response()->json(['data' => $data]);
    }

    public function index($shop_id, $searchValue = null)
    {
        $query = Product::with($this->getProductRelations())
            ->where('published', 1)
            ->where('shop_id', $shop_id)
            ->whereHas('stock', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->whereHas('shop', function ($query) {
                $query->where('status', 1);
            });
    
        // Check if searchValue is provided
        if ($searchValue && !empty($searchValue)) {
            $query->where(function ($subQuery) use ($searchValue) {
                $subQuery->where('name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }
    
        $data = $query->orderBy('id', 'desc')
            ->take(24)
            ->orderByRaw('featured DESC')
            ->get();
    
        return $this->formatResponse($data);
    }
    
    public function load_more($shop_id, $length, $searchValue = null)
    {
        $query = Product::with($this->getProductRelations())
            ->where('published', 1)
            ->where('shop_id', $shop_id)
            ->whereHas('stock', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->whereHas('shop', function ($query) {
                $query->where('status', 1);
            });
    
        // Check if searchValue is provided
        if ($searchValue && !empty($searchValue)) {
            $query->where(function ($subQuery) use ($searchValue) {
                $subQuery->where('name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }
    
        $data = $query->orderBy('id', 'desc')
            ->skip($length)
            ->take(12)
            ->get();
    
        return $this->formatResponse($data);
    }
}
