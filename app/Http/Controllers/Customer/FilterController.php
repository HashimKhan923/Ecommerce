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

                // Perform the search with Scout
        $searchResults = Product::search($request->searchValue)->get();

        // Retrieve IDs of the search results to eager load relationships
        $productIds = $searchResults->pluck('id');

        // Eager load 'user' relationship for the products found
        $data = Product::with(['user', 'category', 'brand', 'shop.shop_policy', 'model', 'stock', 'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'product_varient', 'discount', 'tax', 'shipping'])
                ->where('published', 1)
                ->whereHas('shop', function ($query) {
                    $query->where('status', 1);
                })->whereHas('stock', function ($query) {
                    $query->where('stock', '>', 0);
                })->whereIn('id', $productIds)->get();


        
        // $searchValue = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $request->searchValue);

        // $keywords = explode(' ', $searchValue);
        
        // $data = Product::with(['user', 'category', 'brand', 'shop.shop_policy', 'model', 'stock', 'product_gallery' => function($query) {
        //     $query->orderBy('order', 'asc');
        // }, 'product_varient', 'discount', 'tax', 'shipping'])
        //     ->where('published', 1)
        //     ->whereHas('shop', function ($query) {
        //         $query->where('status', 1);
        //     })->whereHas('stock', function ($query) {
        //         $query->where('stock', '>', 0);
        //     })
        //     ->where(function ($query) use ($keywords, $searchValue) {
        //         // Search in name
        //         $query->where('name', 'LIKE', "%$searchValue%")
        //             ->orWhere(function ($q) use ($keywords) {
        //                 foreach ($keywords as $keyword) {
        //                     $q->orWhere('name', 'LIKE', "%$keyword%");
        //                 }
        //             });
        
        //         // Search in description
        //         $query->orWhere('description', 'LIKE', "%$searchValue%")
        //             ->orWhere(function ($q) use ($keywords) {
        //                 foreach ($keywords as $keyword) {
        //                     $q->orWhere('description', 'LIKE', "%$keyword%");
        //                 }
        //             });
        
        //         // Search in tags (assuming tags is a JSON field)
        //         $query->orWhere(function ($q) use ($keywords) {
        //             foreach ($keywords as $keyword) {
        //                 $q->orWhereJsonContains('tags', $keyword);
        //             }
        //         });
        //     })
        //     ->orderByRaw('CASE 
        //                         WHEN name LIKE ? THEN 1 
        //                         WHEN name LIKE ? THEN 2 
        //                         ELSE 3 
        //                     END', ["%$searchValue%", "%$keywords[0]%"])
        //     ->orderByRaw('featured DESC')
        //     ->get();
        
    

    return response()->json(['data' => $data]);
        
    }

    public function target_search(Request $request)
    {
        $data = Product::with(['user', 'category', 'brand','shop.shop_policy','model', 'stock', 'product_gallery'=> function($query) {
            $query->orderBy('order', 'asc');
        }, 'product_varient', 'discount', 'tax', 'shipping'])
            ->whereJsonContains('start_year', $request->year)
            ->where('brand_id', $request->brand_id)
            ->where('model_id', $request->model_id)
            ->where('published', 1)
            ->whereHas('shop', function ($query) {
                $query->where('status', 1);
            })->whereHas('stock', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->orderByRaw('featured DESC')
            ->get();

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
