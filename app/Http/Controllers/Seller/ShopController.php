<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;

class ShopController extends Controller
{
    public function index($seller_id)
    {
        $data = Shop::where('seller_id',$seller_id)->first();

        return response()->json(['data'=>$data]);
    }

    public function update(Request $request)
    {
        $update = Shop::where('seller_id',$request->seller_id)->first();

        $update->name = $request->name;
        $update->address = $request->address;

        if($request->file('logo')){

            if($update->logo)
            {
                unlink(public_path('ShopLogo/'.$update->logo));
            }
            

            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('ShopLogo'),$filename);
            $update->logo = $filename;
        }

        if($request->file('banner')){

            if($update->banner)
            {
                unlink(public_path('ShopBanner/'.$update->banner));
            }

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('ShopBanner'),$filename);
            $update->banner = $filename;
        }

        $update->save();
    }
}
