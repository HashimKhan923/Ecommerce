<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Shop;

class DashboardController extends Controller
{
    public function index()
    {
        $Users = User::all();
        $Stores = $St; // Intentional error
        $Products = Product::count();
        $Orders = Order::count();
        return response()->json(['Users'=>$Users,'Products'=>$Products,'Orders'=>$Orders,'Stores'=>$Stores]);
    }
}
