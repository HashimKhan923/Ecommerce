<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class FilterController extends Controller
{
    public function search(Request $request)
    {


        // if($request->category_id != null)
        // {
        //     $data = Product::with('user','category','brand','model','stock','product_gallery','product_varient','discount','tax','shipping','deal.deal_product','wholesale')
        //     ->where('name', 'LIKE', '%'.$request->searchValue.'%')
        //     ->where('category_id',$request->category_id)
        //     ->where('published',1)
        //     ->get();
        // }
        // else
        // {
            $data = Product::with('user', 'category', 'brand', 'model', 'stock', 'product_gallery', 'product_varient', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale')
            ->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->searchValue . '%')
                    ->orWhere(function ($query) use ($request) {
                        $query->whereJsonContains('tags', 'LIKE', '%' . $request->searchValue . '%');
                    });
            })
            ->get();
        
        // }

        return response()->json(['data'=>$data]);


    }

    public function target_search(Request $request)
    {
        
        $data = Product::with('user', 'category', 'brand', 'model', 'stock', 'product_gallery', 'product_varient', 'discount', 'tax', 'shipping', 'deal.deal_product', 'wholesale')
        ->whereJsonContains('start_year',$request->year)
        ->where('brand_id', $request->brand_id)
        ->where('model_id', $request->model_id)
        ->get();

        return response()->json(['data'=>$data]);
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


    
        $data = $query->with('user','category','brand','model','stock','product_gallery','product_varient','discount','tax','shipping','deal.deal_product','wholesale')->where('published',1)->get();
    
        return response()->json(['data'=>$data]);
    }
}
