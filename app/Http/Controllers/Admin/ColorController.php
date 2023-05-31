<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    public function index()
    {
      $Colors = Color::all();

      return response()->json(['Colors'=>$Colors]);
    }

    public function create(Request $request)
    {
        $new = new Color();
        $new->name = $request->name;
        $new->code = $request->code;
        $new->save();

        $response = ['status'=>true,"message" => "New Color Added Successfully!"];
        return response($response, 200);

    }


    public function delete($id)
    {
        $file = Color::find($id);
        $file->delete();

        $response = ['status'=>true,"message" => "Color Deleted Successfully!"];
        return response($response, 200);
    }
}
