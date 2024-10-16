<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\UserSearchingKeyword;

class FilterController extends Controller
{
    public function search($searchValue,$length)
    {

        $Keyword = UserSearchingKeyword::firstOrNew(['keyword' => $searchValue]);
        $Keyword->count++;
        $Keyword->save();
        
        $keywords = explode(' ', $searchValue);
        
        $data = Product::with([
            'user', 'category', 'brand', 'shop.shop_policy', 'model', 'stock', 'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            }, 'product_varient', 'discount', 'tax', 'shipping'
        ])
        ->where('published', 1)
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        })->whereHas('stock', function ($query) {
            $query->where('stock', '>', 0);
        })
        ->where(function ($query) use ($keywords,$searchValue) {
            foreach ($keywords as $keyword) {
                $query->where(function ($query) use ($keyword,$searchValue) {
                    $query->where('sku',$keyword)
                    ->orWhere('name', 'LIKE', "%{$keyword}%")
                    // ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhereJsonContains('tags', $searchValue);
                });
            }
        })
        ->orderBy('featured', 'DESC')
        ->skip($length)->take(24)->get();


        
        // $searchValue = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $request->searchValue);

        // $searchValue = $request->searchValue;
        // $keywords = explode(' ', $searchValue);

        // $data = Product::with([
        //     'user', 'category', 'brand', 'shop.shop_policy', 'model', 'stock', 'product_gallery' => function ($query) {
        //         $query->orderBy('order', 'asc');
        //     }, 'product_varient', 'discount', 'tax', 'shipping'
        // ])
        // ->where('published', 1)
        // ->whereHas('shop', function ($query) {
        //     $query->where('status', 1);
        // })->whereHas('stock', function ($query) {
        //     $query->where('stock', '>', 0);
        // })
        // ->where(function ($query) use ($keywords, $searchValue) {
        //     $query->where('sku',$searchValue)
        //     ->orWhere('name', 'LIKE', "%$searchValue%")
        //         ->orWhere(function ($q) use ($keywords) {
        //             foreach ($keywords as $keyword) {
        //                 $q->orWhere('name', 'LIKE', "%$keyword%");
        //             }
        //         })
        //         ->orWhere('description', 'LIKE', "%$searchValue%")
        //         ->orWhere(function ($q) use ($keywords) {
        //             foreach ($keywords as $keyword) {
        //                 $q->orWhere('description', 'LIKE', "%$keyword%");
        //             }
        //         })
        //         ->orWhere(function ($q) use ($keywords) {
        //             foreach ($keywords as $keyword) {
        //                 $q->orWhereJsonContains('tags', $keyword);
        //             }
        //         });
        // })
        // ->when(count($keywords) >= 2, function ($query) use ($searchValue, $keywords) {
        //     return $query->orderByRaw('CASE 
        //         WHEN name = ? THEN 1 
        //         WHEN name LIKE ? THEN 2 
        //         WHEN name LIKE ? THEN 3 
        //         ELSE 4 
        //     END', [$searchValue, "%$searchValue%", "%$keywords[0]%$keywords[1]%"]);
        // }, function ($query) use ($searchValue) {
        //     return $query->orderByRaw('CASE 
        //         WHEN name = ? THEN 1 
        //         WHEN name LIKE ? THEN 2 
        //         ELSE 3 
        //     END', [$searchValue, "%$searchValue%"]);
        // })
        // ->orderBy('featured', 'DESC')
        // ->get();

        return response()->json(['data' => $data]);
        
    }

    public function getSuggestions1($query)
    {
        // Fetch keywords that match the query and order by 'count' field
        $suggestions = UserSearchingKeyword::where('keyword', 'LIKE', "%{$query}%")
            ->select('keyword') // Fetch only the keyword field
            ->take(10) // Limit to 10 suggestions
            ->orderBy('count', 'DESC') // Order by count in descending order
            ->get();
    
        // Return suggestions as JSON
        return response()->json($suggestions);
    }



    public function getSuggestions($query)
    {
        // Break the query into separate words
        $keywords = explode(' ', $query);
    
        // Fetch products where any part of the name matches the keywords
        $products = Product::where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->orWhere('name', 'LIKE', "%{$word}%");
            }
        })
        ->select('name') // Fetch only the name field
        ->take(100) // Get up to 50 products for more combinations
        ->get();
    
        $suggestions = [];
    
        // Loop through each product name
        foreach ($products as $product) {
            $nameWords = explode(' ', $product->name); // Split  product name into words
            
            // Combine words in the name that match the input query
            $matches = [];
            foreach ($nameWords as $nameWord) {
                foreach ($keywords as $keyword) {
                    if (stripos($nameWord, $keyword) !== false) {
                        $matches[] = $nameWord; // Collect matched words
                        break; // Exit the inner loop when match found
                    }
                }
            }
    
            // Join matched words into a suggestion
            if (!empty($matches)) {
                $suggestions[] = implode(' ', $matches);
            }
        }
    
        // Ensure unique suggestions
        $suggestions = array_unique($suggestions);
    
        // Limit to top 10 suggestions
        $suggestions = array_slice($suggestions, 0, 10);
    
        // Return suggestions as JSON response
        return response()->json($suggestions);
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
    
        $data = $query->orderByRaw('featured DESC')->skip($request->length)->take(6)->get();
    
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
        })->where('store_id','!=',74)->orderByRaw('featured DESC')->get();
    
        return response()->json(['data'=>$data]);
    }
}
