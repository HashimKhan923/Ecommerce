<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerFandQ;

class SellerFandQController extends Controller
{
    public function index()
    {
        $data = SellerFandQ::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new SellerFandQ();
        $new->question = $request->question;
        $new->answer = $request->answer;
        $new->save();

        return response()->json(['message'=>'created successfully!',200]);
    }

    public function update(Request $request)
    {
        $update = SellerFandQ::where('id',$request->id)->first();
        $update->question = $request->question;
        $update->answer = $request->answer;
        $update->save();

        return response()->json(['message'=>'update successfully!',200]);
    }

    public function delete($id)
    {
        SellerFandQ::find($id)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);

    }

    public function multi_delete(Request $request)
    {
        SellerFandQ::whereIn('id',$request->ids)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);
    }
}
