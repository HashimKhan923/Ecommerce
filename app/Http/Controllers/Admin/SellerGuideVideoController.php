<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerGuideVideo;

class SellerGuideVideoController extends Controller
{
    public function index()
    {
        $data = SellerGuideVideo::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new SellerGuideVideo();
        $new->link = $request->link;
        $new->save();

        return response()->json(['message'=>'created successfully!',200]);
    }

    public function update(Request $request)
    {
        $update = SellerGuideVideo::where('id',$request->id)->first();
        $update->link = $request->link;
        $update->save();

        return response()->json(['message'=>'update successfully!',200]);
    }

    public function delete($id)
    {
        SellerGuideVideo::find($id)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);

    }

    public function multi_delete(Request $request)
    {
        SellerGuideVideo::whereIn('id',$request->ids)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);
    }
}
