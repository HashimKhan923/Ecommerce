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
            $data = Product::where('name', 'LIKE', '%'.$request->searchValue.'%')
            ->where('category_id',$request->category_id)
            ->get();
        }
        else
        {
            $data = Product::where('name', 'LIKE', '%'.$request->searchValue.'%')->get();
        }

        return response()->json(['data'=>$data]);


    }

    public function price(Request $request)
    {
        $data = Product::where('price','>=',$request->min_price)->where('price','<=',$request->min_price)->get();

        return response()->json(['data'=>$data]);
    }

    public function newest(Request $request)
    {
        
    }
}
