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
    
    $data = Product::with('user', 'category', 'brand', 'shop.shop_policy', 'model', 'stock', 'product_gallery', 'product_varient', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale')
        ->where('published', 1)
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })
        ->where(function ($query) use ($keywords,$searchValue) {
            $query->where('name', 'LIKE', "%$searchValue%");
    
            if (count($keywords) > 1) {
                $query->orWhere(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->where('name', 'LIKE', "%$keyword%");
                    }
                });
            }
    
            $query->orWhere(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->where('name', 'LIKE', "%$keyword%");
                }
            });
        })
        ->orderByRaw('CASE 
                            WHEN name LIKE ? THEN 1 
                            WHEN name LIKE ? THEN 2 
                            ELSE 3 
                        END', ["%$searchValue%", "%$keywords[0]%"])
        // ->orderByRaw('featured DESC')
        ->get();
    

    return response()->json(['data' => $data]);
        
    }

    public function target_search(Request $request)
    {
        $data = Product::with('user', 'category', 'brand','shop.shop_policy','model', 'stock', 'product_gallery', 'product_varient', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale')
            ->whereJsonContains('start_year', $request->year)
            ->where('brand_id', $request->brand_id)
            ->where('model_id', $request->model_id)
            ->where('published', 1)
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


    
        $data = $query->with('user','category','brand','shop','model','stock','product_gallery','product_varient','discount','tax','shipping','deal.deal_product','wholesale')->where('published',1)->get();
    
        return response()->json(['data'=>$data]);
    }
}
