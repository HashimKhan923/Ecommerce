<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index($id)
    {
        $SubscribeUser = SubscribeUser::all();

        $Products = Product::all();
        $Orders = Order::all();

        return response()->json(['SubscribeUser'=>$SubscribeUser,'Products'=>$Products,'Orders'=>$Orders]);
    }
}
