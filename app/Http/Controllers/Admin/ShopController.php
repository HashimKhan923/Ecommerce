<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;

class ShopController extends Controller
{
    public function index()
    {
        $data = Shop::with('seller','shop_policy')->withCount('product')->get();

        return response()->json(['data'=>$data]);
    }

    public function status($id)
    {
       $status = Shop::where('id',$id)->first();

       if($status->status == 1)
       {
        $status->status = 0;
       }
       else
       {
        $status->status = 1;
       }

       $status->save();

       $response = ['status'=>true,"message" => "Status Changed Successfully!"];
       return response($response, 200);
    }

    public function featured($id)
    {
       $featured = Shop::where('id',$id)->first();

       if($featured->featured == 1)
       {
        $featured->featured = 0;
       }
       else
       {
        $featured->featured = 1;
       }

       $featured->save();

       $response = ['status'=>true,"message" => "Changed Successfully!"];
       return response($response, 200);
    }
}
