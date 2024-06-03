<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Validator;

class SubCategoryController extends Controller
{
    public function index()
    {
      $SubCategories = SubCategory::with('category')->get();

      return response()->json(['SubCategories'=>$SubCategories]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "unique:sub_categories,name",
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $new = new SubCategory();
        $new->category_id = $request->category_id;
        $new->name = $request->name;
        if($request->file('banner')){

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('SubCategoryBanner'),$filename);
            $new->banner = $filename;
        }
        if($request->file('icon')){

            $file= $request->icon;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('SubCategoryIcon'),$filename);
            $new->icon = $filename;
        }
        $new->slug = $request->slug;
        // $new->meta_title = $request->meta_title;
        // $new->meta_description = $request->meta_description;
        // $new->meta_keywords = $request->meta_keywords;
        $new->save();

        $response = ['status'=>true,"message" => "New Sub-Category Added Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {




        $update = SubCategory::where('id',$request->id)->first();
        $update->category_id = $request->category_id;
        $update->name = $request->name;
        if($request->file('banner')){

            if($update->banner)
            {
                unlink(public_path('SubCategoryBanner/'.$update->banner));
            }

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('SubCategoryBanner'),$filename);
            $update->banner = $filename;
        }
        if($request->file('icon')){

            if($update->icon)
            {
                unlink(public_path('SubCategoryIcon/'.$update->icon));
            }

            $file= $request->icon;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('SubCategoryIcon'),$filename);
            $update->icon = $filename;
        }
        $update->slug = $request->slug;
        // $update->meta_title = $request->meta_title;
        // $update->meta_description = $request->meta_description;
        // $update->meta_keywords = $request->meta_keywords;
        $update->save();

        $response = ['status'=>true,"message" => "Sub-Category Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = SubCategory::find($id);
        $checkProduct = Product::where('sub_category_id',$id)->first();
        if($checkProduct)
        {
            $response = ['status'=>true,"message" => "first delete the products under this Sub-category!"];
            return response($response, 200);
        }

        if($file->banner)
        {
            unlink(public_path('SubCategoryBanner/'.$file->banner));
        }

        if($file->icon)
        {
            unlink(public_path('SubCategoryIcon/'.$file->icon));
        }


      $file->delete();

        $response = ['status'=>true,"message" => "Sub-Category Deleted Successfully!"];
        return response($response, 200);
    }

    public function multi_delete(Request $request)
    {
        $data = SubCategory::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {

            $checkProduct = Product::where('sub_category_id',$item->id)->first();
            if($checkProduct)
            {
                $response = ['status'=>true,"message" => "first delete the products under '$item->name' category!"];
                return response($response, 200);
            }

            if($item->banner)
            {
                unlink(public_path('SubCategoryBanner/'.$item->banner));
            }
    
            if($item->icon)
            {
                unlink(public_path('SubCategoryIcon/'.$item->icon));
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Sub-Category Deleted Successfully!"];
        return response($response, 200);
    }
}
