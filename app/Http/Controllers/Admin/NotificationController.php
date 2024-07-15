<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $data = Notification::where('customer_id',NULL)->get();

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

    public function view()
    {
        Notification::where('customer_id',NULL)->update(['view_status'=>1]);
    }
}
