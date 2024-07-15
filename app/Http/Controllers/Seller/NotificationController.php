<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;


class NotificationController extends Controller
{
    public function index($seller_id)
    {
        $data = Notification::where('customer_id',$seller_id)->get();

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

    public function view($seller_id)
    {
        Notification::where('customer_id',$seller_id)->update(['view_status'=>1]);
    }
}
