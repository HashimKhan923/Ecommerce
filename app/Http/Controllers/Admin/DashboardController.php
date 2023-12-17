<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $Users = User::all();

        $Products = Product::all();
        $Orders = Order::all();
        

        return response()->json(['Users'=>$Users,'Products'=>$Products,'Orders'=>$Orders]);
    }
}
