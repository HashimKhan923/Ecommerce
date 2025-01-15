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
        $query = Product::with($this->getProductRelations())
            ->where('published', 1)
            ->where('shop_id', $shop_id)
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
    
        // Retrieve products
        $products = $query->take(24)
            ->orderByRaw('featured DESC')
            ->get();
    
        // Fetch categories and their subcategories with product counts
        $categories = Category::whereHas('products', function ($productQuery) use ($shop_id, $searchValue) {
            $productQuery->where('published', 1)->where('shop_id', $shop_id);
    
            if ($searchValue && !empty($searchValue)) {
                $keywords = explode(' ', $searchValue);
                $productQuery->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where('sku', 'LIKE', "%{$keyword}%")
                            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereJsonContains('tags', $keyword);
                    }
                });
            }
        })->withCount(['products' => function ($query) use ($shop_id, $searchValue) {
            $query->where('published', 1)->where('shop_id', $shop_id);
    
            if ($searchValue && !empty($searchValue)) {
                $keywords = explode(' ', $searchValue);
                $query->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where('sku', 'LIKE', "%{$keyword}%")
                            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereJsonContains('tags', $keyword);
                    }
                });
            }
        }])
        ->with(['subCategories' => function ($query) use ($shop_id, $searchValue) {
            $query->whereHas('products', function ($productQuery) use ($shop_id, $searchValue) {
                $productQuery->where('published', 1)->where('shop_id', $shop_id);
    
                if ($searchValue && !empty($searchValue)) {
                    $keywords = explode(' ', $searchValue);
                    $productQuery->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->where('sku', 'LIKE', "%{$keyword}%")
                                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                ->orWhereJsonContains('tags', $keyword);
                        }
                    });
                }
            })
            ->withCount(['products' => function ($query) use ($shop_id, $searchValue) {
                $query->where('published', 1)->where('shop_id', $shop_id);
    
                if ($searchValue && !empty($searchValue)) {
                    $keywords = explode(' ', $searchValue);
                    $query->where(function ($query) use ($keywords) {
                        foreach ($keywords as $keyword) {
                            $query->where('sku', 'LIKE', "%{$keyword}%")
                                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($keyword) . '%'])
                                ->orWhereJsonContains('tags', $keyword);
                        }
                    });
                }
            }]);
        }])->get();
    
        // Format response
        $response = [
            'products' => $products,
            'categories' => $categories,
        ];
    
        return $this->formatResponse($response);
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
