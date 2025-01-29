<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ModelProductController extends Controller
{
    private function getProductsByModel($model_id, $length = null, $searchValue = null)
    {
        $query = Product::with([
            'user','wishlistProduct', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping', 'deal',
            'wholesale', 'shop.shop_policy', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        ->where('model_id', $model_id)
        ->orderByRaw('featured DESC')
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
    
        if ($length !== null) {
            $query->skip($length)->take(12);
        } else {
            $query->take(12);
        }
    
        return $query->get();
    }
    
    public function index($model_id)
    {
        $data = $this->getProductsByModel($model_id);
        return response()->json(['data' => $data]);
    }
    
    public function load_more($model_id, $length, $searchValue = null)
    {
        $data = $this->getProductsByModel($model_id, $length, $searchValue);
        return response()->json(['data' => $data]);
    }
    
}
