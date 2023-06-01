<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    public function index()
    {
      $Sizes = Size::all();

      return response()->json(['Sizes'=>$Sizes]);
    }

    public function create(Request $request)
    {
        $new = new Size();
        $new->name = $request->name;
        $new->save();

        $response = ['status'=>true,"message" => "New Size Added Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {
        $update = Size::where('id',$request->id)->first();
        $update->name = $request->name;
        $update->save();

        $response = ['status'=>true,"message" => "Size Updated Successfully!"];
        return response($response, 200);

    }


    public function delete($id)
    {
        $file = Size::find($id);
        $file->delete();

        $response = ['status'=>true,"message" => "Size Deleted Successfully!"];
        return response($response, 200);
    }
}
