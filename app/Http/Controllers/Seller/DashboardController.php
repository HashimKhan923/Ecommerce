<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Brand;

class DashboardController extends Controller
{
    public function index($id)
    {
        SubscribeUser::where('user_id',$id)->where('end_time','<=',now())->orWhere('product_upload_limit','<',1)->delete();
        $SubscribeUser = SubscribeUser::where('user_id',$id)->first();

        $Products = Product::where('user_id',$id)->get();
        $Orders = Order::where('seller_id',$id)->get();
        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::with('model')->where('is_active',1)->get();

        return response()->json(['SubscribeUser'=>$SubscribeUser,'Products'=>$Products,'Orders'=>$Orders,'Categories'=>$Categories,'Brands'=>$Brands]);
    }
}
