<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FandQ;

class FandQController extends Controller
{
    public function index()
    {
        $data = FandQ::all();

        return response()->json(['data'=>$data]);
    }

    public function create(Request $request)
    {
        $new = new FandQ();
        $new->question = $request->question;
        $new->answer = $request->answer;
        $new->save();

        return response()->json(['message'=>'created successfully!',200]);
    }

    public function update(Request $request)
    {
        $update = FandQ::where('id',$request->id)->first();
        $update->question = $request->question;
        $update->answer = $request->answer;
        $update->save();

        return response()->json(['message'=>'update successfully!',200]);
    }

    public function delete($id)
    {
        FandQ::find($id)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);

    }

    public function multi_delete($ids)
    {
        FandQ::whereIn('id',$id)->delete();

        return response()->json(['message'=>'deleted successfully!',200]);

    }
}
