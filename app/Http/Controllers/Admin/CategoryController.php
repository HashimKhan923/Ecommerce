<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Validator;


class CategoryController extends Controller
{
    public function index()
    {
      $Categories = Category::all();

      return response()->json(['Categories'=>$Categories]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "unique:categories,name",
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

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
        // $new->meta_title = $request->meta_title;
        // $new->meta_description = $request->meta_description;
        // $new->meta_keywords = $request->meta_keywords;
        $new->save();

        $response = ['status'=>true,"message" => "New Category Added Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {




        $update = Category::where('id',$request->id)->first();
        $update->name = $request->name;
        if($request->file('banner')){

            $bannerPath = public_path('CategoryBanner/' . $update->banner);
            if (file_exists($bannerPath) && is_file($bannerPath)) {
                unlink($bannerPath);
            }

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('CategoryBanner'),$filename);
            $update->banner = $filename;
        }
        if($request->file('icon')){

            $iconPath = public_path('CategoryIcon/' . $update->icon);
            if (file_exists($iconPath) && is_file($iconPath)) {
                unlink($iconPath);
            }

            $file= $request->icon;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('CategoryIcon'),$filename);
            $update->icon = $filename;
        }
        $update->slug = $request->slug;
        // $update->meta_title = $request->meta_title;
        // $update->meta_description = $request->meta_description;
        // $update->meta_keywords = $request->meta_keywords;
        $update->save();

        $response = ['status'=>true,"message" => "Category Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Category::find($id);
        $checkProduct = Product::where('category_id',$id)->first();
        if($checkProduct)
        {
            $response = ['status'=>true,"message" => "first delete the products under this category!"];
            return response($response, 200);
        }

        $bannerPath = public_path('CategoryBanner/' . $file->banner);
        if (file_exists($bannerPath) && is_file($bannerPath)) {
            unlink($bannerPath);
        }

        $iconPath = public_path('CategoryIcon/' . $file->icon);
        if (file_exists($iconPath) && is_file($iconPath)) {
            unlink($iconPath);
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

            $checkProduct = Product::where('category_id',$item->id)->first();
            if($checkProduct)
            {
                $response = ['status'=>true,"message" => "first delete the products under '$item->name' category!"];
                return response($response, 200);
            }

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

    public function change_order(Request $request)
    {
        $order = 0;
    
        foreach($request->order as $cat_order)
        {
            $category = Category::find($cat_order['id']);
            if ($category) {
                $category->update(['order' => $order++]);
            }
        }
    }


}
