<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Payout;
use App\Models\Shop;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;


class DashboardController extends Controller
{
    public function index()
    {
        $Users = User::with('time_line','order.order_timeline','seller_order.order_timeline')->get();
        $Stores = Shop::count();
        $Products = Product::count();
        $Orders = Order::all();
        $Payouts = Payout::all();

        $customersViewCount = User::where('user_type','customer')->where('admin_view_status',0)->count();
        $sellersViewCount = User::where('user_type','seller')->where('admin_view_status',0)->count();

        $Notifications = Notification::where('customer_id',null)->get();


        return response()->json(['Users'=>$Users,'Products'=>$Products,'Orders'=>$Orders,'Stores'=>$Stores,'Notifications'=>$Notifications,'Payouts'=>$Payouts,'customersViewCount'=>$customersViewCount,'sellersViewCount'=>$sellersViewCount]);
    }
}
