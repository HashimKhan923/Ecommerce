<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscribeUser;

class DashboardController extends Controller
{
    public function index($id)
    {
        SubscribeUser::where('user_id',$id)->where('end_time','<=',now())->orWhere('product_upload_limit','<',1)->delete();
         $SubscribeUser = SubscribeUser::where('user_id',$id)->first();

         return response()->json(['SubscribeUser'=>$SubscribeUser]);
    }
}
