<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payout;

class PayoutController extends Controller
{
    public function index($id)
    {
        $data = Payout::where('seller_id',$id)->get();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new Payout();
        $new->date = $request->date;
        $new->seller_id = $request->seller_id;
        $new->total_amount_topay = $request->total_amount_topay;
        $new->requested_amount = $request->requested_amount;
        $new->message = $request->message;
        $new->save();

        $response = ['status'=>true,"message" => "Payout Request Created Successfully!"];
        return response($response, 200);
    }

    public function delete($id)
    {
        Payout::find($id)->delete();
        
        $response = ['status'=>true,"message" => "Payout Request Deleted Successfully!"];
        return response($response, 200);
    }
}
