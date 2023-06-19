<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
      $Brands = Brand::all();

      return response()->json(['Brands'=>$Brands]);
    }

    public function create(Request $request)
    {
        $new = new Brand();
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

        $response = ['status'=>true,"message" => "New Brand Added Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {
        $update = Brand::where('id',$request->id)->first();
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

        $response = ['status'=>true,"message" => "Brand Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Brand::find($id);

        $BrandLogo = 'app/public'.$file->logo;
        if (Storage::exists($BrandLogo)) {
            // Delete the file
            Storage::delete($BrandLogo);
        }

      $file->delete();

        $response = ['status'=>true,"message" => "Brand Deleted Successfully!"];
        return response($response, 200);
    }
}
