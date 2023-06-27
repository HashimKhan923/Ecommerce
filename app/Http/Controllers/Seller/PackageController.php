<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\SubscribeUser;
use Carbon\Carbon;

class PackageController extends Controller
{
    public function index()
    {
        $Subscription=Package::all();
        return response()->json(['Package'=>$Package]);
    }

    public function subscribe(Request $request)
    {

      $start_time = Carbon::now();  
      $Package = Package::where('id',$request->subscription_id)->first();
      


      if($Subscription->time_name == 'days')
      {
        $end_time = Carbon::now()->addDay($Subscription->time_number);
      }
      if($Subscription->time_name == 'months')
      {
        $end_time = Carbon::now()->addMonth($Subscription->time_number);
      }
      if($Subscription->time_name == 'years')
      {
        $end_time = Carbon::now()->addYear($Subscription->time_number);
      }


        $new = new SubscribeUser;
        $new->user_id = $request->user_id;
        $new->subscription_id = $request->subscription_id;
        $new->start_time = $start_time;
        $new->end_time = $end_time;
        $new->save();

        return response()->json(['message'=>'Your Subscription Completed Successfully!']);

  
    }

    public function subscribeUser($id)
    {
      $SubscribeUser = SubscribeUser::with('user','plan')->where('user_id',$id)->get();
      return response()->json(['SubscribeUser'=>$SubscribeUser]);
    }
}
