<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;

class RefundController extends Controller
{
    public function index($seller_id)
    {
        $data = Refund::with('order')->where('seller_id',$seller_id)->get();

        return response()->json(['data'=>$data]);
    }

    public function is_approved($refund_id)
    {
        $is_approved = Refund::where('id',$refund_id)->first();

        if($is_approved->seller_approval == 'Not Approved')
        {
            $is_approved->seller_approval = 'Approved';
        }
        else
        {
            $is_approved->seller_approval = 'Not Approved';
        }

        $is_approved->save();
    }
}
