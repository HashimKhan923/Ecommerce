<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Models;
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
        // Product Data
        $totalProducts = Product::where('user_id', $id)->count();
        $featuredProducts = Product::where('user_id', $id)->where('featured', 1)->count();
        $activeProducts = Product::where('user_id', $id)->where('published', 1)->count();
        $draftProducts = Product::where('user_id', $id)->where('published', 0)->count();

        // Order Data
        $orders = Order::where('sellers_id', $id)->get();
        $totalOrders = Order::where('sellers_id', $id)->count();
        $fulfilledOrders = Order::where('sellers_id', $id)->where('delivery_status', 'Delivered')->count();
        $unfulfilledOrders = Order::where('sellers_id', $id)->where('delivery_status', 'Pending')->doesntHave('order_refund')->count();
        $refundedOrders = Order::where('sellers_id', $id)->whereHas('order_refund')->count();
        $totalSales = Order::where('sellers_id', $id)->where('delivery_status', 'Delivered')->sum('amount'); 

        // Payout Data
        $totalPayouts = Payout::where('seller_id', $id)->count();
        $paidPayouts = Payout::where('seller_id', $id)->where('status', 'Paid')->count();
        $unpaidPayouts = Payout::where('seller_id', $id)->where('status', 'Unpaid')->count();
        $totalPayoutAmount = Payout::where('seller_id', $id)->where('status', 'Paid')->sum('amount'); 

        return response()->json([
            // Product Data
            'totalProducts' => $totalProducts,
            'featuredProducts' => $featuredProducts,
            'activeProducts' => $activeProducts,
            'draftProducts' => $draftProducts,
            
            // Order Data
            'orders' => $orders,
            'totalOrders' => $totalOrders,
            'fulfilledOrders' => $fulfilledOrders,
            'unfulfilledOrders' => $unfulfilledOrders,
            'refundedOrders' => $refundedOrders,
            'totalSales' => $totalSales,
            
            // Payout Data
            'totalPayouts' => $totalPayouts,
            'paidPayouts' => $paidPayouts,
            'unpaidPayouts' => $unpaidPayouts,
            'totalPayoutAmount' => $totalPayoutAmount
        ]);
    }


    public function searchByshop($shop_id)
    {
        // Product Data
        $totalProducts = Product::where('shop_id', $shop_id)->count();
        $featuredProducts = Product::where('shop_id', $shop_id)->where('featured', 1)->count();
        $activeProducts = Product::where('shop_id', $shop_id)->where('published', 1)->count();
        $draftProducts = Product::where('shop_id', $shop_id)->where('published', 0)->count();

        // Order Data
        $orders = Shop::with('order.payout')->where('id',$shop_id)->get();
 

        // Payout Data


        return response()->json([
            // Product Data
            'totalProducts' => $totalProducts,
            'featuredProducts' => $featuredProducts,
            'activeProducts' => $activeProducts,
            'draftProducts' => $draftProducts,
            // Payout Data
            'orders' => $orders,

        ]);

    }

    public function collection($seller_id)
    {
        $SellerCategories = Product::where('user_id', $seller_id)->with('category')->distinct()->get(['category_id']);
        $SellerBrands = Product::where('user_id', $seller_id)->with('brand')->distinct()->get(['brand_id']);
        $SellerModels = Product::where('user_id', $seller_id)->with('model')->distinct()->get(['model_id']);
        return response()->json(['SellerCategories'=>$SellerCategories,'SellerBrands'=>$SellerBrands,'SellerModels'=>$SellerModels]);

    }
}
