<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;


class ShopController extends Controller
{
    public function index($seller_id)
    {
        $data = Shop::where('seller_id',$seller_id)->first();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $shop = new Shop();
        $shop->seller_id = $request->seller_id;
        $shop->name = $request->shop_name;
        $shop->address = $request->shop_address;
        if($request->file('logo')){

            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('ShopLogo'),$filename);
            $shop->logo = $filename;
        }

        if($request->file('banner')){

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('ShopBanner'),$filename);
            $shop->banner = $filename;
        }
        $shop->save();


        $response = ['status'=>true,"message" => "Created Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {
        $update = Shop::where('id',$request->id)->first();

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

        $response = ['status'=>true,"message" => "Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Shop::find($id);

        $checkProduct = Product::where('brand_id',$item->id)->first();
        if($checkProduct)
        {
            $response = ['status'=>true,"message" => "first delete the products under this shop!"];
            return response($response, 200);
        }

        if($file->logo)
        {
            unlink(public_path('ShopLogo/'.$file->logo));

        }

        if($file->banner)
        {
            unlink(public_path('ShopBanner/'.$file->banner));
        }

        $file->delete();


        $response = ['status'=>true,"message" => "Deleted Successfully!"];
        return response($response, 200);


    }
}
