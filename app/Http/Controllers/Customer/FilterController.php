<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class FilterController extends Controller
{
    public function search(Request $request)
    {

        if($request->category_id != null)
        {
            $data = Product::with('user','category','brand','model','stock','varient','discount','tax','shipping','deal.deal_product','wholesale')
            ->where('name', 'LIKE', '%'.$request->searchValue.'%')
            ->where('category_id',$request->category_id)
            ->where('published',1)
            ->get();
        }
        else
        {
            $data = Product::where('name', 'LIKE', '%'.$request->searchValue.'%')->get();
        }

        return response()->json(['data'=>$data]);


    }

    public function target_search(Request $request)
    {
        $data = Product::where('year',$request->year)
        ->where('brand_id',$request->brand_id)
        ->where('model_id',$request->model_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function multiSearch(Request $request)
    {
        $query = Product::query();
    
        // Apply filters based on user input

    

        if ($request->min_price != null && $request->max_price != null) {
            $query->where('price', '>=', $request->min_price)->where('price', '<=', $request->max_price);
        } elseif ($request->min_price != null) {
            $query->where('price', '>=', $request->min_price);
        } elseif ($request->man_price != null) {
            $query->where('price', '<=', $request->max_price);
        }
    
        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->brand_id != null) {
            $query->where('brand_id', $request->brand_id);
        }


    
        $data = $query->with('user','category','brand','model','stock','varient','discount','tax','shipping','deal.deal_product','wholesale')->where('published',1)->get();
    
        return response()->json(['data'=>$data]);
    }
}
