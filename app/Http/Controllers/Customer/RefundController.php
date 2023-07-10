<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;

class RefundController extends Controller
{
    public function index($id)
    {
        $data = Refund::where('customer_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {

        $new = new Refund();
        $new->order_id = $request->order_id;
        $new->reason = $request->reason;
        $new->save();

        $response = ['status'=>true,'message'=>'sent successfully!'];
        return response($response,200);

    }

    public function delete($id)
    {
        Refund::find($id)->delete();

        $response = ['status'=>true,'message'=>'deleted successfully!'];
        return response($response,200);

    }

}
