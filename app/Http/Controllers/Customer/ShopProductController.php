<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

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
        $categories = Category::whereHas('product', function ($productQuery) use ($shop_id, $searchValue) {
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
        })->withCount(['product' => function ($query) use ($shop_id, $searchValue) {
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
        ->with(['sub_category' => function ($query) use ($shop_id, $searchValue) {
            $query->whereHas('product', function ($productQuery) use ($shop_id, $searchValue) {
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
            ->withCount(['product' => function ($query) use ($shop_id, $searchValue) {
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
    
    public function load_more($shop_id, $length, $searchValue = null, $cat_id = null, $subcat_id = null)
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
    
        // Apply category filter if cat_id is provided
        if ($cat_id) {
            $query->where('category_id', $cat_id);
        }
    
        // Apply subcategory filter if subcat_id is provided
        if ($subcat_id) {
            $query->where('sub_category_id', $subcat_id);
        }
    
        // Fetch the filtered data
        $data = $query->orderByRaw('featured DESC')
            ->skip($length)
            ->take(12)
            ->get();
    
        // Return the response
        return $this->formatResponse($data);
    }
    
}
