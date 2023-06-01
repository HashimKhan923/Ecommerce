<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
      $Categories = Category::all();

      return response()->json(['Categories'=>$Categories]);
    }

    public function create(Request $request)
    {
        $new = new Category();
        $new->name = $request->name;
        if($request->file('banner')){
            $file= $request->file('banner');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('CategoryBanner'), $filename);
            $new->banner = $filename;
        }
        if($request->file('icon')){
            $file= $request->file('icon');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('CategoryIcon'), $filename);
            $new->icon = $filename;
        }
        $new->slug = $request->slug;
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        $new->save();

        $response = ['status'=>true,"message" => "New Category Added Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {
        $update = Category::where('id',$request->id)->first();
        $update->name = $request->name;
        if($request->file('banner')){

            $image_path = public_path('CategoryBanner/'.$update->banner);
            File::delete($image_path);

            $file= $request->file('banner');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('CategoryBanner'), $filename);
            $update->banner = $filename;

        }
        if($request->file('icon')){
            $image_path = public_path('CategoryIcon/'.$update->icon);
            File::delete($image_path);

            $file= $request->file('icon');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('CategoryIcon'), $filename);
            $update->icon = $filename;
        }
        $update->slug = $request->slug;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        $update->save();

        $response = ['status'=>true,"message" => "Category Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Category::find($id);

        $CategoryBanner = public_path('CategoryBanner/'.$file->banner);
      if (File::exists($CategoryBanner))
      {
          File::delete($CategoryBanner);
      }

      $CategoryIcon = public_path('CategoryIcon/'.$file->icon);
      if (File::exists($CategoryIcon))
      {
          File::delete($CategoryIcon);
      }

      $file->delete();

        $response = ['status'=>true,"message" => "Category Deleted Successfully!"];
        return response($response, 200);
    }


}
