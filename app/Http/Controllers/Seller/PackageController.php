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

       $already_subscriber = SubscribeUser::where('user_id',$request->user_id)->first();

       if($already_subscriber)
       {
          $already_subscriber->product_upload_limit = $already_subscriber->product_upload_limit + $Package->product_upload_limit;
          $already_subscriber->end_time = $already_subscriber->end_time->add($end_time->diff($start_time));
          $already_subscriber->save();

          return response()->json(['message'=>'Your Package upgrated successfully!']);
       }
       else
       {
        $new = new SubscribeUser;
        $new->user_id = $request->user_id;
        $new->package_id = $request->subscription_id;
        $new->product_upload_limit = $Package->product_upload_limit;
        $new->start_time = $start_time;
        $new->end_time = $end_time;
        $new->save();

        return response()->json(['message'=>'Your Subscription Completed Successfully!']);
       }





  
    }

    public function subscribeUser()
    {
      $SubscribeUser = SubscribeUser::with('user','plan')->where('user_id',auth()->user()->id)->get();
      return response()->json(['SubscribeUser'=>$SubscribeUser]);
    }
}
