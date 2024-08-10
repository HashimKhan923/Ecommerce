<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\UserSearchingKeyword;

class FilterController extends Controller
{
    public function search(Request $request)
    {
        $Keyword = UserSearchingKeyword::firstOrNew(['keyword' => $request->searchValue]);
        $Keyword->count++;
        $Keyword->save();
    
        $searchValue = $request->searchValue;
        $keywords = explode(' ', $searchValue);
    
        $data = Product::with([
            'user', 'category', 'brand', 'shop.shop_policy', 'model', 'stock',
            'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'product_varient', 'discount', 'tax', 'shipping'
        ])
        ->where('published', 1)
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })
        ->where(function ($query) use ($searchValue, $keywords) {
            // Prioritize results where the full search value (both keywords together) appears
            $query->where('name', 'LIKE', "%$searchValue%")
                // Prioritize results where all keywords appear in any order
                ->orWhere(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    }
                })
                // Add products where either keyword appears
                ->orWhere(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhere('name', 'LIKE', "%$keyword%");
                    }
                });
        })
        // Order the results by the appearance of both keywords together, then separately
        ->orderByRaw('CASE 
                        WHEN name LIKE ? THEN 1 
                        WHEN name LIKE ? THEN 2 
                        ELSE 3 
                    END', ["%$searchValue%", "%$keywords[0]%$keywords[1]%"])
        ->orderBy('featured', 'DESC')
        ->get();
    
        return response()->json(['data' => $data]);
    }
    
    

    public function target_search(Request $request)
    {
        $query = Product::with([
            'user', 
            'category', 
            'brand',
            'shop.shop_policy',
            'model', 
            'stock', 
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 
            'product_varient', 
            'discount', 
            'tax', 
            'shipping'
        ])
        ->where('published', 1)
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        });
    
        if ($request->has('year')) {
            $query->whereJsonContains('start_year', $request->year);
        }
    
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
    
        if ($request->has('model_id')) {
            $query->where('model_id', $request->model_id);
        }
    
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
    
        $data = $query->orderByRaw('featured DESC')->get();
    
        return response()->json(['data' => $data]);
    }
    


    public function multiSearch(Request $request)
    {
        $query = Product::query();
        
        if ($request->category_id != null) {

            $query->where('category_id', $request->category_id);
        }

        if ($request->min_price != null && $request->max_price != null) {

            $query->where('price', '>=', $request->min_price)->where('price', '<=', $request->max_price);

        } elseif ($request->min_price != null) {

            $query->where('price', '>=', $request->min_price);

        } elseif ($request->max_price != null) {

            $query->where('price', '<=', $request->max_price);
        }
    


        if ($request->brand_id != null) {

            $query->where('brand_id', $request->brand_id);
        }


    
        $data = $query->with('user','category','brand','shop','model','stock','product_gallery','product_varient','discount','tax','shipping','deal.deal_product','wholesale')->where('published',1)->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })->orderByRaw('featured DESC')->get();
    
        return response()->json(['data'=>$data]);
    }
}
