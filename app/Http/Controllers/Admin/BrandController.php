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

            // if(public_path('Brand/'.$update->logo))
            // {
            //     unlink(public_path('Brand/'.$update->logo));
            // }

            $file= $request->logo;
            $filename= date('YmdHis').$file->getClientOriginalName();
            $file->move(public_path('Brand'),$filename);
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

        $checkProduct = Product::where('brand_id',$id)->first();
        if($checkProduct)
        {
            $response = ['status'=>true,"message" => "first delete the products under this brand!"];
            return response($response, 200);
        }


        if(public_path('Brand/'.$file->logo))
        {
            unlink(public_path('Brand/'.$file->logo));
        }

      $file->delete();

        $response = ['status'=>true,"message" => "Brand Deleted Successfully!"];
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
            if($item->logo)
            {

                unlink(public_path('Brand/'.$file->logo));
            }

            $item->delete();
        }

        

        $response = ['status'=>true,"message" => "Brands Deleted Successfully!"];
        return response($response, 200);
    }

    
}
