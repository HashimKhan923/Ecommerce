<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models;
use Storage;

class ModelController extends Controller
{
    public function index()
    {
      $Models = Models::all();

      return response()->json(['Models'=>$Models]);
    }

    public function create(Request $request)
    {
        $new = new Models();
        $new->brand_id = $request->brand_id;
        $new->name = $request->name;
        if($request->file('logo')){
            $file= $request->file('logo');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $new->logo = $filename;
        }
        $new->slug = $request->slug;
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        $new->save();

        $response = ['status'=>true,"message" => "New Models Added Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {
        $update = Models::where('id',$request->id)->first();
        $update->brand_id = $request->brand_id;
        $update->name = $request->name;
        if($request->file('logo')){

            $image_path = 'app/public'.$update->logo;
            if(Storage::exists($image_path))
            {
                Storage::delete($image_path);
            }
            

            $file= $request->file('logo');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $update->logo = $filename;
        }
        $update->slug = $request->slug;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        $update->save();

        $response = ['status'=>true,"message" => "Models Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Models::find($id);

        $ModelsLogo = 'app/public'.$file->logo;
        if (Storage::exists($ModelsLogo)) {
            // Delete the file
            Storage::delete($ModelsLogo);
        }

      $file->delete();

        $response = ['status'=>true,"message" => "Models Deleted Successfully!"];
        return response($response, 200);
    }
}
