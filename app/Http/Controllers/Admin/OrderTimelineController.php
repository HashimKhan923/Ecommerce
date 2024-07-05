<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderTimeline;


class OrderTimelineController extends Controller
{
    public function create(Request $request)
    {
        OrderTimeline::create([
            'customer_id' => $request->user_id,
            'time_line' => $request->time_line
        ]);

        return response()->json(['message'=>'Timeline created successfully!']);
    }

    public function update(Request $request)
    {
        $orderTimeline = OrderTimeline::find($request->timeline_id);

        if ($orderTimeline) {
            $orderTimeline->update([
                'time_line' => $request->time_line
            ]);
        }

        return response()->json(['message'=>'Timeline updated successfully!']);
    }

    public function delete($timeline_id)
    {
        OrderTimeline::where('id',$timeline_id)->delete();

        return response()->json(['message'=>'Timeline deleted successfully!']);
    }
}
