<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Validator;


class BrandController extends Controller
{
    public function index()
    {
      $Brands = Brand::with('model')->get();

      return response()->json(['Brands'=>$Brands]);
    }

    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "name" => "unique:brands,name",
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        


        $new = new Brand();
        $new->name = $request->name;
        if($request->file('logo')){

            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Brand'),$filename);
            $new->logo = $filename;
        }

        if($request->file('banner')){

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Brand'),$filename);
            $new->banner = $filename;
        }
        $new->slug = $request->slug;
        $new->meta_title = $request->meta_title;
        $new->meta_description = $request->meta_description;
        $new->meta_keywords = $request->meta_keywords;
        $new->save();

        $response = ['status'=>true,"message" => "New Brand Added Successfully!"];
        return response($response, 200);

    }

    public function update(Request $request)
    {




        $update = Brand::where('id',$request->id)->first();
        $update->name = $request->name;
        if($request->file('logo')){

            if(public_path('Brand/'.$update->logo))
            {
                unlink(public_path('Brand/'.$update->logo));
            }

            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Brand'),$filename);
            $update->logo = $filename;
        }

        if($request->file('banner')){

            // if(public_path('Brand/'.$update->banner))
            // {
            //     unlink(public_path('Brand/'.$update->banner));
            // }

            $file= $request->banner;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Brand'),$filename);
            $update->banner = $filename;
        }
        $update->slug = $request->slug;
        $update->meta_title = $request->meta_title;
        $update->meta_description = $request->meta_description;
        $update->meta_keywords = $request->meta_keywords;
        $update->save();

        $response = ['status'=>true,"message" => "Brand Updated Successfully!"];
        return response($response, 200);

    }

    public function delete($id)
    {
        $file = Brand::find($id);
    
        $checkProduct = Product::where('brand_id', $id)->first();
        if ($checkProduct) {
            $response = ['status' => true, "message" => "First delete the products under this brand!"];
            return response($response, 200);
        }
    
        $logoPath = public_path('Brand/' . $file->logo);
        if (file_exists($logoPath) && is_file($logoPath)) {
            unlink($logoPath);
        }

        $bannerPath = public_path('Brand/' . $file->banner);
        if (file_exists($bannerPath) && is_file($bannerPath)) {
            unlink($bannerPath);
        }
    
        $file->delete();
    
        $response = ['status' => true, "message" => "Brand Deleted Successfully!"];
        return response($response, 200);
    }
    

    public function multi_delete(Request $request)
    {
        $data = Brand::whereIn('id',$request->ids)->get();

        foreach($data as $item)
        {
            $checkProduct = Product::where('brand_id',$item->id)->first();
            if($checkProduct)
            {
                $response = ['status'=>true,"message" => "first delete the products under '$item->name' brand!"];
                return response($response, 200);
            }
            $logoPath = public_path('Brand/' . $item->logo);
            if (item_exists($logoPath) && is_file($logoPath)) {
                unlink($logoPath);
            }
    
            $bannerPath = public_path('Brand/' . $file->banner);
            if (file_exists($bannerPath) && is_file($bannerPath)) {
                unlink($bannerPath);
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Brands Deleted Successfully!"];
        return response($response, 200);
    }

    
}
