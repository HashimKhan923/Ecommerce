<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Illuminate\Support\Facades\File;

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
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('BrandLogo'), $filename);
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
        $update = Category::where('id',$request->id)->first();
        $update->name = $request->name;
        if($request->file('logo')){
            $file= $request->file('logo');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('BrandLogo'), $filename);
            $new->logo = $filename;
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
        $file = Category::find($id);

        $BrandLogo = public_path('BrandLogo/'.$file->logo);
      if (File::exists($BrandLogo))
      {
          File::delete($BrandLogo);
      }

      $file->delete();

        $response = ['status'=>true,"message" => "Brand Deleted Successfully!"];
        return response($response, 200);
    }
}
