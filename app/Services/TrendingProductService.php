<?php

namespace App\Services;

use App\Models\Product;
use App\Models\AiTrendingProduct;

class TrendingProductService
{
    public function getTrendingProducts(int $limit = 30, int $perKeyword = 4)
    {
        $trendingKeywords = AiTrendingProduct::pluck('names')->toArray();
        $trendingProducts = collect();

        foreach ($trendingKeywords as $keyword) {
            $words = array_filter(explode(' ', trim($keyword)));
            $collected = collect();

            // Helper closure to build the query
            $buildQuery = function ($words) {
                return Product::select('id', 'name', 'shop_id', 'price', 'featured')
                    ->with([
                        'shop:id,name,status',
                        'discount:id,product_id,discount,discount_type',
                        'product_gallery' => function ($query) {
                            $query->select('id', 'product_id', 'image', 'order')
                                  ->orderBy('order', 'asc');
                        },
                        'product_varient:id,product_id,price,discount_price',
                        'reviews' => function ($query) {
                            $query->select('id', 'product_id', 'user_id', 'rating')
                                  ->with('user:id,name');
                        }
                    ])
                    ->where('published', 1)
                    ->orderByRaw('RAND()')
                    ->whereHas('shop', fn($q) => $q->where('status', 1))
                    ->where(function($q) use ($words) {
                        foreach ($words as $word) {
                            $q->orWhere('name', 'like', '%' . $word . '%');
                        }
                    });
            };

            // Tier 1: At least 3 matches
            $tier1 = $buildQuery($words)->get()->filter(function ($product) use ($words) {
                return $this->countMatches($product->name, $words) >= 3;
            });
            $collected = $collected->merge($tier1)->take($perKeyword);

            // Tier 2: At least 2 matches
            if ($collected->count() < $perKeyword) {
                $tier2 = $buildQuery($words)->get()->filter(function ($product) use ($words) {
                    return $this->countMatches($product->name, $words) == 2;
                });
                $collected = $collected->merge($tier2)->take($perKeyword);
            }

            // Tier 3: At least 1 match
            if ($collected->count() < $perKeyword) {
                $tier3 = $buildQuery($words)->get()->filter(function ($product) use ($words) {
                    return $this->countMatches($product->name, $words) == 1;
                });
                $collected = $collected->merge($tier3)->take($perKeyword);
            }

            $trendingProducts = $trendingProducts->merge($collected);
        }

        return $trendingProducts->unique('id')->take($limit)->values();
    }

    private function countMatches(string $productName, array $words): int
    {
        $count = 0;
        foreach ($words as $word) {
            if (stripos($productName, $word) !== false) {
                $count++;
            }
        }
        return $count;
    }
}