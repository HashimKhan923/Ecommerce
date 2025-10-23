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
use App\Models\Deal;
use App\Models\Chat;
use App\Models\OrderForcast;
use Carbon\Carbon;
use DB;


class DashboardController extends Controller
{
    public function index($user_id = 0, $shop_id = 0)
    {
        // Build dynamic filter
        $productQuery = Product::query();
        $orderQuery = Order::query();
        $payoutQuery = Payout::query();

        if ($user_id != 0) {
            $productQuery->where('user_id', $user_id);
            $orderQuery->where('sellers_id', $user_id);
            $payoutQuery->where('seller_id', $user_id);
        }

        if ($shop_id != 0) {
            $productQuery->where('shop_id', $shop_id);
            $orderQuery->where('shop_id', $shop_id);
            $payoutQuery->where('shop_id', $shop_id);
        }

        // Product Data
        $products = $productQuery->selectRaw("
            COUNT(*) as totalProducts,
            SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featuredProducts,
            SUM(CASE WHEN published = 1 THEN 1 ELSE 0 END) as activeProducts,
            SUM(CASE WHEN published = 0 THEN 1 ELSE 0 END) as draftProducts
        ")->first();

        // Order Data
        $stats = $orderQuery->selectRaw("
            COUNT(*) as totalOrders,
            SUM(CASE WHEN delivery_status = 'Delivered' THEN 1 ELSE 0 END) as fulfilledOrders,
            SUM(CASE WHEN delivery_status = 'Pending' THEN 1 ELSE 0 END) as unfulfilledOrders,
            SUM(CASE WHEN delivery_status = 'Confirmed' THEN 1 ELSE 0 END) as confirmedOrders,
            SUM(CASE WHEN delivery_status = 'Cancelled' THEN 1 ELSE 0 END) as refundedOrders,
            SUM(CASE WHEN delivery_status = 'Delivered' THEN amount ELSE 0 END) as totalSales
        ")->first();

        // Payout Data
        $payouts = $payoutQuery->selectRaw("
            COUNT(*) as totalPayouts,
            SUM(CASE WHEN status = 'Paid' THEN 1 ELSE 0 END) as paidPayouts,
            SUM(CASE WHEN status = 'Un Paid' THEN 1 ELSE 0 END) as unpaidPayouts,
            SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as totalPayoutAmount
        ")->first();

        // Forecast
        $orderForcast = OrderForcast::where('seller_id', $user_id)
            ->orderBy('month', 'asc')
            ->get();

        // Unread messages
        $unReadMessageCount = Chat::where('reciver_id', $user_id)
            ->where('status', 'unread')
            ->count();

        // Last 12 months orders (for chart)
        $orders = Order::when($user_id, fn($q) => $q->where('sellers_id', $user_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id))
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->select('amount', 'created_at')
            ->get();

        return response()->json([
            // Product Data
            'totalProducts' => $products->totalProducts ?? 0,
            'featuredProducts' => $products->featuredProducts ?? 0,
            'activeProducts' => $products->activeProducts ?? 0,
            'draftProducts' => $products->draftProducts ?? 0,

            // Orders
            'totalOrders' => $stats->totalOrders ?? 0,
            'fulfilledOrders' => $stats->fulfilledOrders ?? 0,
            'unfulfilledOrders' => $stats->unfulfilledOrders ?? 0,
            'confirmedOrders' => $stats->confirmedOrders ?? 0,
            'refundedOrders' => $stats->refundedOrders ?? 0,
            'totalSales' => $stats->totalSales ?? 0,

            // Payouts
            'totalPayouts' => $payouts->totalPayouts ?? 0,
            'paidPayouts' => $payouts->paidPayouts ?? 0,
            'unpaidPayouts' => $payouts->unpaidPayouts ?? 0,
            'totalPayoutAmount' => $payouts->totalPayoutAmount ?? 0,

            // Other
            'orderForcast' => $orderForcast,
            'unReadMessageCount' => $unReadMessageCount,
            'orders' => $orders,
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
        $orders = Shop::with('order.payout','order.order_refund')->where('id',$shop_id)->get();
 

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
