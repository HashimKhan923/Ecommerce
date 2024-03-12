<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Payout;
use App\Models\SellerGuideVideo;
use App\Models\SellerFandQ;
use App\Models\Shop;



class DashboardController extends Controller
{
    public function index($id)
    {
        // $package = SubscribeUser::where('end_time', '<=', now())->first();

        // if ($package) {
        //     Product::where('user_id', $package->user_id)->where('featured', 1)->update(['featured' => 0]);
        //     $package->delete();
        // }

        $SubscribeUser = SubscribeUser::where('user_id',$id)->first();
        $Products = Product::where('user_id',$id)->get();
        $Orders = Order::with('order_refund')->where('sellers_id',$id)->get();
        $Payouts = Payout::where('seller_id',$id)->get();
        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::with('model')->where('is_active',1)->get();
        $SellerFandQ = SellerFandQ::all();
        $SellerGuideVideo = SellerGuideVideo::all();


        return response()->json(['SubscribeUser'=>$SubscribeUser,'Products'=>$Products,'Orders'=>$Orders,'Payouts'=>$Payouts,'Categories'=>$Categories,'Brands'=>$Brands,'SellerFandQ'=>$SellerFandQ,'SellerGuideVideo'=>$SellerGuideVideo]);
    }

    public function searchByshop($shop_id)
    {
        $Shop = Shop::with('product','order','shop_policy')->where('id',$shop_id)->get();

        return response()->json(['Shop'=>$Shop]);

    }

    public function collection($seller_id)
    {
        $SellerCategories = Product::where('user_id', $seller_id)->with('category')->distinct()->get(['category_id']);
        $SellerBrands = Product::where('user_id', $seller_id)->with('brand')->distinct()->get(['brand_id']);
        $SellerModels = Product::where('user_id', $seller_id)->with('model')->distinct()->get(['model_id']);
        return response()->json(['SellerCategories'=>$SellerCategories,'SellerBrands'=>$SellerBrands,'SellerModels'=>$SellerModels]);

    }
}
