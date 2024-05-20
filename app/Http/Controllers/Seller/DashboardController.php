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
use App\Models\MyCustomer;
use App\Models\Notification;



class DashboardController extends Controller
{
    public function index($id)
    {


        $SubscribeUser = SubscribeUser::where('user_id',$id)->first();
        $Products = Product::where('user_id',$id)->get();
        $Orders = Order::with('order_refund')->where('sellers_id',$id)->get();
        $Payouts = Payout::where('seller_id',$id)->get();
        $Categories = Category::where('is_active',1)->get();
        $Brands = Brand::with('model')->where('is_active',1)->get();
        $SellerFandQ = SellerFandQ::all();
        $SellerGuideVideo = SellerGuideVideo::all();
        $TotalSale = MyCustomer::where('seller_id',$id)->sum('sale') ?? 0;
        $Notifications = Notification::where('customer_id',$id)->get();


        return response()->json(['SubscribeUser'=>$SubscribeUser,'Products'=>$Products,'Orders'=>$Orders,'Payouts'=>$Payouts,'Categories'=>$Categories,'Brands'=>$Brands,'SellerFandQ'=>$SellerFandQ,'SellerGuideVideo'=>$SellerGuideVideo,'TotalSale'=>$TotalSale,'Notifications'=>$Notifications]);
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
