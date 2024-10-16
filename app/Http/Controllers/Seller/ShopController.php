<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;
use App\Models\SellerPolicy;


class ShopController extends Controller
{
    public function index($seller_id)
    {
        $data = Shop::with('shop_policy')->withCount('product')->where('seller_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function edit($id)
    {
        $data = Shop::with('shop_policy')->withCount('product')->where('id',$id)->first();

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

        $policy = new SellerPolicy();
        $policy->shop_id = $shop->id;
        $policy->store_policy = $request->store_policy;
        $policy->return_policy = $request->return_policy;
        $policy->about = $request->about;
        $policy->save();


        Notification::create([
            'notification' => 'New Shop Created Successfully!'
        ]);


        $response = ['status'=>true,"message" => "Created Successfully!",'store_id'=>$shop->id];
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


        $policy = SellerPolicy::where('shop_id',$update->id)->first();
        if(!$policy)
        {
            $policy = new SellerPolicy();
            $policy->shop_id = $update->id;
        }
        $policy->store_policy = $request->store_policy;
        $policy->return_policy = $request->return_policy;
        $policy->about = $request->about;
        $policy->save();

        $response = ['status'=>true,"message" => "Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Shop::find($id);

        $checkProduct = Product::where('shop_id',$id)->first();
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
}
