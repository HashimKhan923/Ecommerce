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

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('CategoryBanner'),$filename);
            $new->banner = $filename;
        }
        if($request->file('icon')){

            $file= $request->icon;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('CategoryIcon'),$filename);
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

            if($update->banner)
            {
                unlink(public_path('CategoryBanner/'.$update->banner));
            }

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('CategoryBanner'),$filename);
            $update->banner = $filename;
        }
        if($request->file('icon')){

            if($update->icon)
            {
                unlink(public_path('CategoryIcon/'.$update->icon));
            }

            $file= $request->icon;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('CategoryIcon'),$filename);
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

        if($file->banner)
        {
            unlink(public_path('CategoryBanner/'.$file->banner));
        }

        if($file->icon)
        {
            unlink(public_path('CategoryIcon/'.$file->icon));
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
            if($item->banner)
            {
                unlink(public_path('CategoryBanner/'.$item->banner));
            }
    
            if($item->icon)
            {
                unlink(public_path('CategoryIcon/'.$item->icon));
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Category Deleted Successfully!"];
        return response($response, 200);
    }


}
