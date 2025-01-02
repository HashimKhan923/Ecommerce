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
      $SubCategories = SubCategory::with('category')->withCount('product')->orderBy('order', 'asc')->get();


      return response()->json(['SubCategories'=>$SubCategories]);
    }

    public function create(Request $request)
    {
        $errors = [];
        
        foreach ($request->sub_categories as $SubCatData) {
            $validator = Validator::make($SubCatData, [
                "name" => "required|unique:sub_categories,name",
                // "category_id" => "required|integer",
                // "slug" => "required|string",
                "banner" => "nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048",
                "icon" => "nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048",
            ]);
    
            if ($validator->fails()) {
                $errors[] = $validator->errors()->all();
                continue;
            }
    
            $new = new SubCategory();
            // $new->category_id = $SubCatData['category_id'];
            $new->name = $SubCatData['name'];
            
            if (isset($SubCatData['banner']) && $SubCatData['banner']) {
                $file = $SubCatData['banner'];
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $file->move(public_path('SubCategoryBanner'), $filename);
                $new->banner = $filename;
            }
    
            if (isset($SubCatData['icon']) && $SubCatData['icon']) {
                $file = $SubCatData['icon'];
                $filename = date('YmdHis') . $file->getClientOriginalName();
                $file->move(public_path('SubCategoryIcon'), $filename);
                $new->icon = $filename;
            }
    
            $new->slug = $SubCatData['slug'];
            $new->save();
        }
    
        if (!empty($errors)) {
            return response(['errors' => $errors], 422);
        }
    
        $response = ['status' => true, "message" => "New Sub-Category Added Successfully!"];
        return response($response, 200);
    }
    

    public function update(Request $request)
    {




        $update = SubCategory::where('id',$request->id)->first();
        // $update->category_id = $request->category_id;
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

    public function change_order(Request $request)
    {
        foreach ($request->sub_category_order as $index => $sub_category_id) 
        {
            DB::table('category_sub_category')
                ->where('id', $sub_category_id)
                ->update(['order' => $index + 1]);
        }
    }
}
