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

    public function index($shop_id)
    {
        $data = Product::with($this->getProductRelations())
            ->where('published', 1)
            ->where('shop_id', $shop_id)
            ->whereHas('stock', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->whereHas('shop', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('id', 'desc')
            ->take(24)
            ->get();

        return $this->formatResponse($data);
    }

    public function load_more($shop_id, $length)
    {
        $data = Product::with($this->getProductRelations())
            ->where('published', 1)
            ->where('shop_id', $shop_id)
            ->whereHas('stock', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->whereHas('shop', function ($query) {
                $query->where('status', 1);
            })
            ->orderBy('id', 'desc')
            ->skip($length)
            ->take(12)
            ->get();

        return $this->formatResponse($data);
    }
}
