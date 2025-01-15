<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class ShopProductController extends Controller
{
    private function getProductRelations()
    {
        return [
            'user','wishlistProduct',
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
            'deal',
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
        $query = Product::with([
            'category',
            'sub_category',
        ])
        ->where('published', 1)
        ->where('shop_id', $shop_id)
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })
        ->select('category_id', 'sub_category_id', DB::raw('COUNT(*) as product_count'))
        ->groupBy('category_id', 'sub_category_id')
        ->get();
    
            // Apply search logic if a search value is provided
            if ($searchValue && !empty($searchValue)) {
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
    
        $data = $query->take(24)
            ->orderByRaw('featured DESC')
            ->get();
    
        return $this->formatResponse($data);
    }
    
    public function load_more($shop_id, $length, $searchValue = null)
    {
        $query = Product::with($this->getProductRelations())
            ->where('published', 1)
            ->where('shop_id', $shop_id)
            // ->whereHas('stock', function ($query) {
            //     $query->where('stock', '>', 0);
            // })
            ->whereHas('shop', function ($query) {
                $query->where('status', 1);
            });
    
            // Apply search logic if a search value is provided
            if ($searchValue && !empty($searchValue)) {
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
    
        $data = $query->orderByRaw('featured DESC')
            ->skip($length)
            ->take(12)
            ->get();
    
        return $this->formatResponse($data);
    }
}
