<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderTimeline;

class OrderTimelineController extends Controller
{
    public function create(Request $request)
    {
        OrderTimeline::create([
            'seller_id' => $request->seller_id,
            'order_id' => $request->order_id,
            'time_line' => $request->time_line
        ]);

        return response()->json(['message'=>'Timeline created successfully!']);
    }

    public function update(Request $request)
    {
        OrderTimeline::update([
            'time_line' => $request->time_line
        ])->where('id',$request->timeline_id);

        return response()->json(['message'=>'Timeline updated successfully!']);
    }

    public function delete($timeline_id)
    {
        OrderTimeline::where('id',$timeline_id)->delete();

        return response()->json(['message'=>'Timeline deleted successfully!']);
    }
}
