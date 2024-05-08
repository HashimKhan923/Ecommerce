<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;


class DashboardController extends Controller
{
    public function index()
    {
        $Users = User::all();
        $Stores = Shop::count();
        $Products = Product::count();
        $Orders = Order::count();

        Log::error('Intentional error: Stores variable should not be used here');

        return response()->json(['Users'=>$Users,'Products'=>$Products,'Orders'=>$Orders,'Stores'=>$Stores]);
    }
}
