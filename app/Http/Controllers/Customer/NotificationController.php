<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index($customer_id)
    {
        $data = Notification::where('customer_id',$customer_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function delete($notification_id)
    {
        Notification::find($notification_id)->delete();

        return response()->json(['messsage'=>'deleted successfully']);
    }

    public function multi_delete(Request $request)
    {
        Notification::whereIn('id',$request->ids)->delete();

        return response()->json(['messsage'=>'deleted successfully']);
    }

    public function view($user_id)
    {
        Notification::where('customer_id',$user_id)->update(['view_status'=>1]);
    }
}
