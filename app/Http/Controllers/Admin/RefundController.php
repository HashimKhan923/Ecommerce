<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;

class RefundController extends Controller
{
    public function index()
    {
        $data = Refund::all();

        return response()->json(['data'=>$data]);
    }

    public function approved_refunds()
    {
        $data = Refund::where('refund_status','Approved')->get();

        return response()->json(['data'=>$data]);
    }

    public function rejected_refunds()
    {
        $data = Refund::where('refund_status','Rejected')->get();

        return response()->json(['data'=>$data]);
    }

    public function change_status(Request $request)
    {
        $change = Refund::where('id',$request->id)->fisrt();
        $change->refund_status = $request->refund_status;
        $change->save();

        $response = ['status'=>true,"message" => "Status Changed Successfully!"];
        return response($response, 200);
    }
}
