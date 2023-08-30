<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerPackage;
use App\Models\SubscribeUser;
use Carbon\Carbon;

class PackageController extends Controller
{
    public function index()
    {
        $Package=SellerPackage::all();
        return response()->json(['Package'=>$Package]);
    }

    public function subscribe(Request $request)
    {

      $start_time = Carbon::now();  
      $Package = SellerPackage::where('id',$request->subscription_id)->first();
      


      if($Package->time_name == 'days')
      {
        $end_time = Carbon::now()->addDay($Package->time_number);
      }
      if($Package->time_name == 'months')
      {
        $end_time = Carbon::now()->addMonth($Package->time_number);
      }
      if($Package->time_name == 'years')
      {
        $end_time = Carbon::now()->addYear($Package->time_number);
      }


        $new = new SubscribeUser;
        $new->user_id = auth()->user()->id;
        $new->subscription_id = $request->subscription_id;
        $new->product_upload_limit = $Package->product_upload_limit;
        $new->start_time = $start_time;
        $new->end_time = $end_time;
        $new->save();

        return response()->json(['message'=>'Your Subscription Completed Successfully!']);

  
    }

    public function subscribeUser()
    {
      $SubscribeUser = SubscribeUser::with('user','plan')->where('user_id',auth()->user()->id)->get();
      return response()->json(['SubscribeUser'=>$SubscribeUser]);
    }
}
