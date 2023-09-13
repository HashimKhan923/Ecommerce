<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

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
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $new->banner = $filename;
        }
        if($request->file('icon')){
            $file= $request->file('icon');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
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

            $image_path = 'app/public'.$update->banner;
            if(Storage::exists($image_path))
            {
                Storage::delete($image_path);
            }

            $file= $request->file('banner');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
            $update->banner = $filename;

        }
        if($request->file('icon')){
            $image_path = 'app/public'.$update->icon;
            Storage::delete($image_path);

            $file= $request->file('icon');
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->storeAs('public', $filename);
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

        $CategoryBanner = 'app/public'.$file->banner;
      if (Storage::exists($CategoryBanner))
      {
          Storage::delete($CategoryBanner);
      }

      $CategoryIcon = 'app/public'.$file->icon;
      if (Storage::exists($CategoryIcon))
      {
          Storage::delete($CategoryIcon);
      }

      $file->delete();

        $response = ['status'=>true,"message" => "Category Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = Category::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {
            $path1 = 'app/public'.$item->banner;
            if (Storage::exists($path1)) {
                // Delete the file
                Storage::delete($path1);
            }

            $path2 = 'app/public'.$item->icon;
            if (Storage::exists($path2)) {
                // Delete the file
                Storage::delete($path2);
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Category Deleted Successfully!"];
        return response($response, 200);
    }


}
